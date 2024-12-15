<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');

// Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

// Check if the necessary session variables are set
if (!isset($_SESSION['selected_ferry'], $_SESSION['selected_accommodation'], $_SESSION['passenger_details'], $_SESSION['departure'], $_SESSION['destination'], $_SESSION['departure_date'])) {
    die("Error: Booking details are missing. Please fill out the booking form and try again.");
}

// Get session data
$selected_ferry_id = $_SESSION['selected_ferry'];
$selected_accommodation = $_SESSION['selected_accommodation'];
$passenger_details = $_SESSION['passenger_details'];
$departure = $_SESSION['departure'];
$destination = $_SESSION['destination'];
$departure_date = $_SESSION['departure_date'];

// Fetch ferry and accommodation details
$query = $conn->prepare("
    SELECT ferries.ferry_name, accommodation.accom_type, accommodation_prices.price 
    FROM accommodation_prices
    INNER JOIN ferries ON ferries.ferry_id = accommodation_prices.ferry_id
    INNER JOIN accommodation ON accommodation.accom_price_id = accommodation_prices.accom_id
    WHERE accommodation_prices.ferry_id = ? AND accommodation_prices.accom_id = ?
");
$query->bind_param("ii", $selected_ferry_id, $selected_accommodation['id']);
$query->execute();
$result = $query->get_result();

$ferry_name = 'N/A';
$accom_type = 'N/A';
$accom_price = 0;

if ($row = $result->fetch_assoc()) {
    $ferry_name = $row['ferry_name'];
    $accom_type = $row['accom_type'];
    $accom_price = $row['price'];
}

// Discount rates based on passenger type
$discount_rates = [
    'regular' => 0,       // No discount
    'student' => 0.2,     // 20% discount
    'pwd' => 0.2,         // 20% discount
    'senior' => 0.2,      // 20% discount, without VAT
    'child' => 0.5,       // 50% discount
    'infant' => 1         // Free (100% discount)
];

// Calculate total cost
$total_cost = 0;
foreach ($passenger_details as $index => $passenger) {
    if (is_array($passenger)) {
        $passenger_type = strtolower($passenger['passenger_type']);
        $discount_rate = $discount_rates[$passenger_type] ?? 0;
        $discounted_fare = $accom_price * (1 - $discount_rate);
        $total_cost += $discounted_fare;
    } else {
        $total_cost += $accom_price; // Default to no discount
    }
}

// Page title
$pageTitle = "Payment Details";
include 'header.php';
?>

<section class="container mt-5">
    <div class="card payment-details p-4">
        <div class="payment-header text-center">
            <h2 class="mb-3">GCash Payment Details</h2>
            <p class="mb-0">Please make your payment using GCash</p>
        </div>

        <!-- GCash Payment Details -->
        <div class="gcash-info bg-light p-4 rounded mb-4">
            <h4 class="text-primary mb-3">GCash Payment Information</h4>
            <p><span class="info-label">GCash Number:</span> <strong>09271234567</strong></p>
            <p><span class="info-label">GCash Owner:</span> <strong>Julius Canonio</strong></p>
            <p><span class="info-label">Total Amount to Pay:</span> <strong>₱<?= number_format($total_cost, 2) ?></strong></p>
            <p class="text-muted">Once you have completed the payment, please enter the reference number below.</p>
        </div>

        <!-- Reference Number Form -->
        <div class="reference-form">
            <h4 class="text-primary mb-3">Enter GCash Reference Number</h4>
            <form id="paymentForm" action="process_payment.php" method="POST">
                <div class="mb-3">
                    <label for="reference_number" class="form-label">Reference Number</label>
                    <input type="text" class="form-control" id="reference_number" name="reference_number" required>
                </div>
                <div class="mb-3">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#confirmPaymentModal">Submit Reference Number</button>
                </div>
            </form>
        </div>

        <!-- Navigation -->
        <div class="d-flex justify-content-between mt-4 action-buttons">
            <a href="review_booking.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to Review
            </a>
        </div>
    </div>
</section>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmPaymentModal" tabindex="-1" aria-labelledby="confirmPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmPaymentModalLabel">Confirm Payment Submission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to submit this reference number for your payment?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('paymentForm').submit();">Confirm</button>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
