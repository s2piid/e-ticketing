<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');

// Ensure the user is an admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle booking status update (confirm or decline)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_booking_id'])) {
    $booking_id = (int) $_POST['confirm_booking_id'];
    $status = $_POST['status']; // 'confirmed' or 'declined'

    // Ensure the status is either 'confirmed' or 'declined'
    if ($status !== 'confirmed' && $status !== 'declined') {
        echo "Invalid status.";
        exit();
    }

    // Prepare and execute the query to update the status
    $update_query = $conn->prepare("UPDATE bookings SET status = ? WHERE booking_id = ?");
    $update_query->bind_param("si", $status, $booking_id);
    
    if ($update_query->execute()) {
        // If successful, redirect to view_reservations.php to refresh the table
        header("Location: view_reservation.php");
        exit();
    } else {
        // Error message if the update fails
        echo "Error updating the booking status.";
    }
}
?>
