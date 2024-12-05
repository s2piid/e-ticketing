<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');

if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

if (!isset($_SESSION['departure']) || !isset($_SESSION['destination']) || !isset($_SESSION['departure_date']) || !isset($_SESSION['passengers']) || !isset($_POST['ferry_id'])) {
    header("Location: travel_details.php");
    exit();
}

$departure = $_SESSION['departure'];
$destination = $_SESSION['destination'];
$departure_date = $_SESSION['departure_date'];
$passengers = $_SESSION['passengers'];
$ferry_id = $_POST['ferry_id'];

// Fetch ferry name
$sql = "SELECT ferry_name FROM ferries WHERE ferry_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ferry_id);
$stmt->execute();
$stmt->bind_result($ferry_name);
$stmt->fetch();
$stmt->close();

// Calculate total fare (for simplicity, assume a fixed price per passenger)
$price_per_passenger = 500;  // Example fixed price
$total_fare = $price_per_passenger * $passengers;

$pageTitle = "Payment Summary";
include 'header.php';
?>

<section class="container mt-5">
    <h2 class="text-center mb-4">Payment Summary</h2>
    <div class="card shadow p-4">
        <h5>Ferry: <?= htmlspecialchars($ferry_name) ?></h5>
        <p>Departure: <?= htmlspecialchars($departure) ?> to <?= htmlspecialchars($destination) ?></p>
        <p>Date: <?= htmlspecialchars($departure_date) ?></p>
        <p>Number of Passengers: <?= $passengers ?></p>
        <div class="mt-4">
            <strong>Total Fare: ₱<?= number_format($total_fare, 2) ?></strong>
        </div>

        <form method="POST" action="confirm_booking.php" class="mt-4">
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success">Confirm and Pay</button>
            </div>
        </form>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>