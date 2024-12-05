<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');
$title = "Book Ferry"; // Page-specific title
include('header.php'); // Include the header

// Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

// Fetch ports for step 1
$departureQuery = "SELECT DISTINCT departure_port FROM ferry_schedule WHERE status = 'active'";
$departureResult = $conn->query($departureQuery);

$arrivalQuery = "SELECT DISTINCT arrival_port FROM ferry_schedule WHERE status = 'active'";
$arrivalResult = $conn->query($arrivalQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Ferry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Book Your Ferry</h2>
        <form method="POST" action="passenger_details.php" class="mt-4">
            <div class="mb-3">
                <label for="departure" class="form-label">Departure</label>
                <select name="departure" id="departure" class="form-select" required>
                    <option value="" disabled selected>Select Departure</option>
                    <?php while ($row = $departureResult->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($row['departure_port']) ?>">
                            <?= htmlspecialchars($row['departure_port']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="destination" class="form-label">Destination</label>
                <select name="destination" id="destination" class="form-select" required>
                    <option value="" disabled selected>Select Destination</option>
                    <?php while ($row = $arrivalResult->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($row['arrival_port']) ?>">
                            <?= htmlspecialchars($row['arrival_port']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="departure_date" class="form-label">Date of Departure</label>
                <input type="date" name="departure_date" id="departure_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="passengers" class="form-label">Passengers</label>
                <input type="number" name="passengers" id="passengers" class="form-control" min="1" required>
            </div>
            <button type
            <?php include('footer.php'); ?>