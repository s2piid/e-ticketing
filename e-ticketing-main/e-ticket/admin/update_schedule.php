<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');

// Ensure the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Your update schedule form handling logic here...

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Update Schedule</title>
</head>
<body>
<h3>Update Ferry Schedule</h3>
<form method="POST" action="">
    <label for="ferry_name">Ferry Name</label>
    <input type="text" name="ferry_name" id="ferry_name" required>
    <label for="departure_port">Departure Port</label>
    <input type="text" name="departure_port" id="departure_port" required>
    <label for="arrival_port">Arrival Port</label>
    <input type="text" name="arrival_port" id="arrival_port" required>
    <label for="departure_time">Departure Time</label>
    <input type="time" name="departure_time" id="departure_time" required>
    <label for="arrival_time">Arrival Time</label>
    <input type="time" name="arrival_time" id="arrival_time" required>
    <label for="status">Status</label>
    <select name="status" id="status" required>
        <option value="Active">Active</option>
        <option value="Inactive">Inactive</option>
    </select>
    <button type="submit" name="update_schedule">Update Schedule</button>
</form>
</body>
</html>
