<?php
session_start();

// Check if booking was successful
if (isset($_SESSION['success'])) {
    echo "<h1>Booking Successful!</h1>";
    echo "<p>" . $_SESSION['success'] . "</p>";
    // Optionally clear the success message after displaying
    unset($_SESSION['success']);
} else {
    echo "<h1>No booking found!</h1>";
}
?>