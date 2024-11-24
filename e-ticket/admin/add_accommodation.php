<?php
session_start();
include('C:/xampp/htdocs/e-ticket/config.php');

// Ensure the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Your add accommodation form handling logic here...

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Accommodation</title>
</head>
<body>
<h3>Add Accommodation</h3>
<form method="POST" action="">
    <label for="ferry_name">Ferry Name</label>
    <input type="text" name="ferry_name" id="ferry_name" required>
    <label for="accom_type">Accommodation Type</label>
    <input type="text" name="accom_type" id="accom_type" required>
    <label for="price">Price</label>
    <input type="number" name="price" id="price" required>
    <button type="submit" name="add_accommodation">Add Accommodation</button>
</form>
</body>
</html>
