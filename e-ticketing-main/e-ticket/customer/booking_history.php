<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');

// Ensure the user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

// Retrieve customer ID from session
$customer_id = (int) $_SESSION['customer_id'];

// Handle booking cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_cancel_booking_id'])) {
    $cancel_booking_id = (int) $_POST['confirm_cancel_booking_id'];
    $cancel_query = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE booking_id = ? AND fk_user_id = ?");
    $cancel_query->bind_param("ii", $cancel_booking_id, $customer_id);
    $cancel_query->execute();
    header("Location: booking_history.php?cancel_success=1");
    exit();
}

// Query to fetch booking history along with passenger names
$query = $conn->prepare("
    SELECT 
        bookings.booking_id, bookings.departure, bookings.destination, bookings.departure_date, 
        bookings.total_cost, bookings.booking_date, bookings.status, bookings.reference_number,
        bookings.first_name, bookings.last_name
    FROM bookings
    WHERE bookings.fk_user_id = ?
    ORDER BY bookings.booking_date DESC
");
$query->bind_param("i", $customer_id);
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
    <title>Booking History</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <div class='header'>
        <h1>Booking History</h1>
        <div class='back-button'>
            <a href='customer_dashboard.php' class="btn btn-primary">Back</a>
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Departure</th>
                <th>Destination</th>
                <th>Departure Date</th>
                <th>Booking Date</th>
                <th>Status</th>
                <th>Reference Number</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['first_name']) ?></td>
            <td><?= htmlspecialchars($row['last_name']) ?></td>
            <td><?= htmlspecialchars($row['departure']) ?></td>
            <td><?= htmlspecialchars($row['destination']) ?></td>
            <td><?= htmlspecialchars($row['departure_date']) ?></td>
            <td><?= htmlspecialchars($row['booking_date']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td><?= htmlspecialchars($row['reference_number']) ?></td>
            <td>
    <?php if ($row['status'] === 'confirmed'): ?>
        <!-- Show the modal trigger button for confirmed tickets -->
        <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#viewTicketModal" data-booking-id="<?= htmlspecialchars($row['booking_id']) ?>">
            View Ticket
        </button>
    <?php elseif ($row['status'] !== 'cancelled'): ?>
        <!-- Show the cancel button if the booking is not confirmed or cancelled -->
        <button class="btn btn-danger btn-sm" 
            data-toggle="modal" 
            data-target="#confirmCancelModal" 
            data-booking-id="<?= htmlspecialchars($row['booking_id']) ?>" 
            data-departure="<?= htmlspecialchars($row['departure']) ?>" 
            data-destination="<?= htmlspecialchars($row['destination']) ?>">
            Cancel
        </button>
    <?php else: ?>
        <span class="text-muted">Cancelled</span>
    <?php endif; ?>
    </td>
        </tr>
    <?php endwhile; ?>
</tbody>
    </table>

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

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmCancelModal" tabindex="-1" role="dialog" aria-labelledby="confirmCancelModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmCancelModalLabel">Confirm Cancellation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="booking_history.php">
                    <div class="modal-body">
                        <p>Are you sure you want to cancel this booking?</p>
                        <input type="hidden" name="confirm_cancel_booking_id" id="confirmCancelBookingID">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Yes, Cancel</button>
                    </div>
                </form>
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

        // Set the booking ID for cancellation
        $('#confirmCancelModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); 
            var bookingID = button.data('booking-id');
            var modal = $(this);
            modal.find('#confirmCancelBookingID').val(bookingID);
        });
    </script>
</body>
</html>

<?php include 'footer.php'; ?>
