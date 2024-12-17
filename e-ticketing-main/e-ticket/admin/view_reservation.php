<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');

// Ensure the user is an admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle booking confirmation/decline by admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_booking_id'])) {
    $booking_id = (int) $_POST['confirm_booking_id'];
    $status = $_POST['status']; // 'confirmed' or 'declined'
    
    // Prepare and execute the query to update the status
    $update_query = $conn->prepare("UPDATE bookings SET status = ? WHERE booking_id = ?");
    $update_query->bind_param("si", $status, $booking_id);
    
    if ($update_query->execute()) {
        // If successful, redirect to the same page to refresh the table
        header("Location: view_reservations.php");
        exit();
    } else {
        // Error message if the update fails
        echo "Error updating the booking status.";
    }
}

// Query to fetch all bookings for admin review
$query = $conn->prepare("
    SELECT 
        bookings.booking_id, bookings.fk_user_id, bookings.fk_ferry_id, bookings.departure, bookings.destination, 
        bookings.departure_date, bookings.total_cost, bookings.booking_date, bookings.status, bookings.reference_number,
        bookings.first_name, bookings.middle_name, bookings.last_name, bookings.gender, bookings.birth_date, 
        bookings.civil_status, bookings.nationality, bookings.address, bookings.passenger_type, bookings.accom_price, 
        bookings.valid_id, bookings.discount
    FROM bookings
    ORDER BY bookings.booking_id DESC
");
$query->execute();
$result = $query->get_result();

// Page title
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class='header'>
        <h1>Bookings</h1>
        <div class='back-button'>
            <a href='admin_dashboard.php' class="btn btn-primary">Back</a>
        </div>
    </div>

    <div class="table-container">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>User ID</th>
                    <th>Ferry ID</th>
                    <th>Departure</th>
                    <th>Departure Date</th>
                    <th>Name</th>
                    <th>Reference Number</th>
                    <th>Passenger Type</th>
                    <th>Total Cost</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['booking_id']) ?></td>
                        <td><?= htmlspecialchars($row['fk_user_id']) ?></td>
                        <td><?= htmlspecialchars($row['fk_ferry_id']) ?></td>
                        <td><?= htmlspecialchars($row['departure']) ?></td>
                        <td><?= htmlspecialchars($row['departure_date']) ?></td>
                        <td><?= htmlspecialchars($row['first_name']) ?> <?= htmlspecialchars($row['last_name']) ?></td>
                        <td><?= htmlspecialchars($row['reference_number']) ?></td>
                        <td><?= htmlspecialchars($row['passenger_type']) ?></td>
                        <td><?= htmlspecialchars($row['total_cost']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <?php if ($row['status'] == 'pending'): ?>
                                <!-- Admin can confirm or decline the booking -->
                                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#confirmationModal" data-action="confirmed" data-booking-id="<?= $row['booking_id'] ?>" data-user-name="<?= $row['first_name'] . ' ' . $row['last_name'] ?>">Confirm</button>
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirmationModal" data-action="declined" data-booking-id="<?= $row['booking_id'] ?>" data-user-name="<?= $row['first_name'] . ' ' . $row['last_name'] ?>">Decline</button>
                            <?php elseif ($row['status'] == 'confirmed'): ?>
                                <!-- Option for the admin to view the ticket -->
                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewTicketModal" data-booking-id="<?= htmlspecialchars($row['booking_id']) ?>" data-reference-number="<?= htmlspecialchars($row['reference_number']) ?>">View Ticket</button>
                            <?php else: ?>
                                <span class="text-muted"><?= $row['status'] ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="11" class="text-center">No bookings available.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- View Ticket Modal -->
    <div class="modal fade" id="viewTicketModal" tabindex="-1" role="dialog" aria-labelledby="viewTicketModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewTicketModalLabel">Ticket Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="ticketDetailsContent">
                    <!-- Ticket details will be loaded here via AJAX -->
                </div>
                <div class="modal-footer">
                    <a href="" id="downloadTicketLink" class="btn btn-success" download>Download Ticket</a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for confirmation -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirm Action</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to <span id="modal-action"></span> the booking for <span id="modal-user-name"></span>?
                </div>
                <div class="modal-footer">
                    <form method="POST" action="update_booking_status.php">
                        <input type="hidden" name="confirm_booking_id" id="modal-booking-id">
                        <input type="hidden" name="status" id="modal-status">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // AJAX to load ticket details into the modal
        $('#viewTicketModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); 
            var bookingID = button.data('booking-id'); 

            var modal = $(this);
            
            $.ajax({
                url: 'fetch_ticket_details.php',
                type: 'GET',
                data: { booking_id: bookingID },
                success: function(response) {
                    modal.find('#ticketDetailsContent').html(response);
                    modal.find('#downloadTicketLink').attr('href', 'download_ticket.php?booking_id=' + bookingID);
                }
            });
        });

        // When the modal is shown, populate it with the action (Confirm/Decline) and the booking info
        $('#confirmationModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var action = button.data('action');  // Action (confirm or decline)
            var bookingId = button.data('booking-id'); // Booking ID
            var userName = button.data('user-name');  // User name
            
            var modal = $(this);
            modal.find('#modal-action').text(action);
            modal.find('#modal-user-name').text(userName);
            modal.find('#modal-booking-id').val(bookingId);
            modal.find('#modal-status').val(action);
        });
    </script>
</body>
</html>

<?php include 'footer.php'; ?>
