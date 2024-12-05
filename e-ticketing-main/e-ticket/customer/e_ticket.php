<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');

if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

if (!isset($_SESSION['passenger_data'])) {
    header("Location: travel_details.php");
    exit();
}

$pageTitle = "E-Ticket";
include 'header.php';
?>
<section>
    <h2>Your E-Ticket</h2>
    <div class="card">
        <p><strong>Booking ID:</strong> <?= uniqid('BOOK') ?></p>
        <p><strong>Ferry:</strong> <?= htmlspecialchars($_SESSION['ferry_name']) ?></p>
        <p><strong>Passengers:</strong></p>
        <ul>
            <?php foreach ($_SESSION['passenger_data'] as $passenger): ?>
                <li><?= htmlspecialchars($passenger['first_name']) . ' ' . htmlspecialchars($passenger['last_name']) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
<?php include 'footer.php'; ?>