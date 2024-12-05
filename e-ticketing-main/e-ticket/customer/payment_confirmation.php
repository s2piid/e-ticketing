<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');

// Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

// Check if the payment status and message are set in the session
if (!isset($_SESSION['payment_method']) || !isset($_SESSION['payment_message']) || !isset($_SESSION['total_cost'])) {
    die("Error: Payment details are missing.");
}

$payment_method = $_SESSION['payment_method'];
$payment_message = $_SESSION['payment_message'];
$total_cost = $_SESSION['total_cost'];
$gcash_number = $_SESSION['gcash_number'] ?? '';

// Page Title
$pageTitle = "Payment Confirmation";
include 'header.php';
?>

<section class="container mt-5">
    <div class="card shadow p-4">
        <h2 class="text-center mb-4">Payment Confirmation</h2>

        <!-- Payment Status and Message -->
        <h5>Status: <?= htmlspecialchars(ucfirst($payment_method)) ?></h5>
        <p><?= htmlspecialchars($payment_message) ?></p>

        <!-- Display GCash info if selected -->
        <?php if ($payment_method == 'GCash'): ?>
            <p><strong>GCash Number (Admin's):</strong> <?= htmlspecialchars($gcash_number) ?></p>
            <p><strong>Instructions:</strong> Please send your payment to the GCash number above. Total amount to be paid: ₱<?= number_format($total_cost, 2) ?></p>
        <?php endif; ?>

        <!-- Display total cost -->
        <h5 class="mt-4">Total Cost: ₱<?= number_format($total_cost, 2) ?></h5>

        <!-- Reference Number Input Form -->
        <form action="submit_payment.php" method="POST">
            <div class="form-group">
                <label for="reference_number">Reference Number:</label>
                <input type="text" id="reference_number" name="reference_number" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success mt-3">Submit Payment</button>
        </form>

        <?php if (isset($_SESSION['payment_status']) && $_SESSION['payment_status'] == 'success'): ?>
            <div class="alert alert-success mt-4">Booking submitted successfully!</div>
        <?php endif; ?>

        <!-- Options to Return -->
        <div class="d-flex justify-content-between mt-4">
            <a href="index.php" class="btn btn-primary">Go to Home</a>
            <a href="review_booking.php" class="btn btn-secondary">Review Booking Again</a>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>