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

// Ensure a booking ID is provided
if (!isset($_GET['cancel_booking_id'])) {
    header("Location: booking_history.php");
    exit();
}

$cancel_booking_id = (int) $_GET['cancel_booking_id'];

// Fetch booking details to confirm cancellation
$query = $conn->prepare("
    SELECT booking_id, departure, destination, departure_date, total_cost, status
    FROM bookings
    WHERE booking_id = ? AND fk_user_id = ?
");
$query->bind_param("ii", $cancel_booking_id, $customer_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    // If no matching booking found
    header("Location: booking_history.php");
    exit();
}

$booking = $result->fetch_assoc();

// Handle cancellation confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cancel_query = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE booking_id = ? AND fk_user_id = ?");
    $cancel_query->bind_param("ii", $cancel_booking_id, $customer_id);
    $cancel_query->execute();
    header("Location: booking_history.php?cancel_success=1");
    exit();
}

// Page title
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Cancellation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .btn {
            padding: 10px 15px;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Confirm Cancellation</h2>
        <p>Are you sure you want to cancel the following booking?</p>
        <ul>
            <li><strong>Booking ID:</strong> <?= htmlspecialchars($booking['booking_id']) ?></li>
            <li><strong>Departure:</strong> <?= htmlspecialchars($booking['departure']) ?></li>
            <li><strong>Destination:</strong> <?= htmlspecialchars($booking['destination']) ?></li>
            <li><strong>Departure Date:</strong> <?= htmlspecialchars($booking['departure_date']) ?></li>
            <li><strong>Total Cost:</strong> ₱<?= htmlspecialchars(number_format($booking['total_cost'], 2)) ?></li>
            <li><strong>Status:</strong> <?= htmlspecialchars($booking['status']) ?></li>
        </ul>
        <form action="confirm_cancel.php?cancel_booking_id=<?= $cancel_booking_id ?>" method="POST">
            <button type="submit" class="btn btn-danger">Yes, Cancel Booking</button>
            <a href="booking_history.php" class="btn btn-secondary">No, Go Back</a>
        </form>
    </div>
</body>
</html>

<?php include 'footer.php'; ?>
