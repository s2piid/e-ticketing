<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');

// Ensure the user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo "You must be logged in to view ticket details.";
    exit();
}

$customer_id = $_SESSION['customer_id'];
$booking_id = isset($_GET['booking_id']) ? $_GET['booking_id'] : null;

$query = "SELECT ticket_number, first_name, last_name, departure, destination, departure_date, status, passenger_type, total_cost
          FROM bookings
          WHERE fk_user_id = ? AND booking_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $customer_id, $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$ticket = $result->fetch_assoc();

if (!$ticket) {
    echo "Ticket not found!";
    exit();
}

echo "
    <p><strong>Ticket Number:</strong> " . htmlspecialchars($ticket['ticket_number']) . "</p>
    <p><strong>Passenger Name:</strong> " . htmlspecialchars($ticket['first_name']) . " " . htmlspecialchars($ticket['last_name']) . "</p>
    <p><strong>Departure:</strong> " . htmlspecialchars($ticket['departure']) . "</p>
    <p><strong>Destination:</strong> " . htmlspecialchars($ticket['destination']) . "</p>
    <p><strong>Departure Date:</strong> " . htmlspecialchars($ticket['departure_date']) . "</p>
    <p><strong>Status:</strong> " . htmlspecialchars($ticket['status']) . "</p>
    <p><strong>Passenger Type:</strong> " . htmlspecialchars($ticket['passenger_type']) . "</p>
    <p><strong>Total Cost:</strong> Php " . number_format($ticket['total_cost'], 2) . "</p>
";
