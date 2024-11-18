<?php
session_start();
include ('C:/xampp/htdocs/e-ticket/config.php'); // Ensure the correct path to your config.php

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch admin information (optional)
$admin_id = $_SESSION['admin_id'];
$stmt = $conn->prepare("SELECT username FROM Users WHERE user_id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$stmt->bind_result($admin_username);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="C:\xampp\htdocs\e-ticket\style.css">
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        padding: 20px;
    }
    .dashboard-container {
        max-width: 800px;
        margin: 0 auto;
        background: #fff;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .welcome-message {
        margin-bottom: 20px;
    }
    .btn-logout {
        display: inline-block;
        padding: 10px 20px;
        background: #d9534f;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        border: none;
        cursor: pointer;
    }
    .btn-logout:hover {
        background: #c9302c;
    }
    .menu {
        margin-top: 20px;
    }
    .menu a {
        display: inline-block;
        margin: 5px;
        padding: 10px 15px;
        background: #5bc0de;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
    }
    .menu a:hover {
        background: #31b0d5;
    }
</style>
</head>
<body>
<div class="dashboard-container">
    <h2>Admin Dashboard</h2>
    <p class="welcome-message">Welcome, <strong><?php echo htmlspecialchars($admin_username); ?></strong>!</p>
    
    <div class="menu">
        <a href="manage_users.php">Manage Users</a>
        <a href="view_reservations.php">View Reservations</a>
        <a href="manage_ferry.php">Manage Ferry Schedule</a>
        <a href="reports.php">Reports</a>
        <button onclick="logout()" class="btn-logout">Logout</button>
    </div>
</div>

<script>
    function logout() {
        if (confirm("Are you sure you want to log out?")) {
            window.location.href = 'admin_login.php';
        }
    }
</script>
</body>
</html>
