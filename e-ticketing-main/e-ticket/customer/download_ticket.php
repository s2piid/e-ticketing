<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');

// Ensure the user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$booking_id = isset($_GET['booking_id']) ? $_GET['booking_id'] : null;

// Query the database for the ticket information
$query = "SELECT booking_id, ticket_number, departure, destination, departure_date, first_name, last_name, status, passenger_type, total_cost
          FROM bookings
          WHERE fk_user_id = ? AND booking_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $customer_id, $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$ticket = $result->fetch_assoc();

if ($ticket) {
    // Display ticket details
    echo "<h1>Ticket Details</h1>";
    echo "<p><strong>Ticket Number:</strong> " . htmlspecialchars($ticket['ticket_number']) . "</p>";
    echo "<p><strong>Passenger Name:</strong> " . htmlspecialchars($ticket['first_name']) . " " . htmlspecialchars($ticket['last_name']) . "</p>";
    echo "<p><strong>Departure:</strong> " . htmlspecialchars($ticket['departure']) . "</p>";
    echo "<p><strong>Destination:</strong> " . htmlspecialchars($ticket['destination']) . "</p>";
    echo "<p><strong>Departure Date:</strong> " . htmlspecialchars($ticket['departure_date']) . "</p>";
    echo "<p><strong>Status:</strong> " . htmlspecialchars($ticket['status']) . "</p>";
    echo "<p><strong>Passenger Type:</strong> " . htmlspecialchars($ticket['passenger_type']) . "</p>";
    echo "<p><strong>Total Cost:</strong> " . htmlspecialchars($ticket['total_cost']) . "</p>";

    // Provide the download link for the ticket
    echo "<a href='download_ticket.php?booking_id=" . htmlspecialchars($ticket['booking_id']) . "' class='btn btn-success'>Download Ticket</a>";
} else {
    echo "Ticket information not found!";
}
?>
