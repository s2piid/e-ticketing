<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if the user ID is passed in the URL
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    
    // Delete the user by setting the deleted_at timestamp
    $delete_query = "UPDATE users SET deleted_at = NOW() WHERE user_id = ?";
    if ($stmt = $conn->prepare($delete_query)) {
        $stmt->bind_param('i', $user_id);
        if ($stmt->execute()) {
            header("Location: manage_users.php?success=User has been deleted.");
            exit();
        } else {
            header("Location: manage_users.php?error=Error deleting user.");
            exit();
        }
        $stmt->close();
    }
} else {
    // If no user ID is set, redirect to manage users with an error
    header("Location: manage_users.php?error=No user ID specified.");
    exit();
}
?>
