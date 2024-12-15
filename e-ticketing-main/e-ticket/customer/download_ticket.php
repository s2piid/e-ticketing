<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');

// Ensure the user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

if (isset($_GET['booking_id'])) {
    $booking_id = (int)$_GET['booking_id'];

    // Query to fetch the ticket details based on the booking ID
    $query = $conn->prepare("
        SELECT tickets.ticket_number, tickets.full_name, tickets.departure, tickets.destination, tickets.departure_date, tickets.ticket_status
        FROM tickets
        JOIN bookings ON tickets.fk_booking_id = bookings.booking_id
        WHERE bookings.booking_id = ? AND bookings.fk_user_id = ?
    ");
    $query->bind_param("ii", $booking_id, $_SESSION['customer_id']);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $ticket = $result->fetch_assoc();

        // Generate ticket content (this can be a PDF or other formats)
        $ticket_content = "
            Ticket Number: " . $ticket['ticket_number'] . "\n
            Full Name: " . $ticket['full_name'] . "\n
            Departure: " . $ticket['departure'] . "\n
            Destination: " . $ticket['destination'] . "\n
            Departure Date: " . $ticket['departure_date'] . "\n
            Status: " . $ticket['ticket_status'] . "\n
        ";

        // Send the ticket as a downloadable file
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="ticket_' . $ticket['ticket_number'] . '.txt"');
        echo $ticket_content;
        exit();
    } else {
        echo "Ticket not found.";
    }
} else {
    echo "Invalid booking ID.";
}
?>
