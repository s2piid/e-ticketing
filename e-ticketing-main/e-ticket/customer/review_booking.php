<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');

// Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}
// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";
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

// Fetch ferry details
$query = $conn->prepare("
    SELECT ferries.ferry_name 
    FROM ferries 
    WHERE ferries.ferry_id = ?
");
$query->bind_param("i", $selected_ferry_id);
$query->execute();
$result = $query->get_result();

$ferry_name = 'N/A';

if ($row = $result->fetch_assoc()) {
    $ferry_name = $row['ferry_name'];
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

// Page title
$pageTitle = "Review Your Booking";
include 'header.php';
?>

<section class="container mt-5">
    <div class="card booking-review p-4">
        <div class="booking-header text-center">
            <h2 class="mb-3">Review Your Booking</h2>
            <p class="mb-0">Please verify all details before proceeding to payment</p>
        </div>

        <!-- Ferry and Trip Information -->
        <div class="bg-light p-4 rounded mb-4">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="text-primary mb-3">Ferry Details</h4>
                    <p><span class="info-label">Ferry Name:</span> <?= htmlspecialchars($ferry_name) ?></p>
                    <p><span class="info-label">From:</span> <?= htmlspecialchars($departure) ?></p>
                    <p><span class="info-label">To:</span> <?= htmlspecialchars($destination) ?></p>
                    <p><span class="info-label">Date:</span> <?= htmlspecialchars($departure_date) ?></p>
                </div>
                <div class="col-md-6">
                    <h4 class="text-primary mb-3">Accommodation Details</h4>
                    <?php if ($selected_accommodation): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="info-label"><?= htmlspecialchars($selected_accommodation['type']) ?></span>
                            <span class="price-tag">₱<?= htmlspecialchars(number_format($selected_accommodation['price'], 2)) ?></span>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No accommodation details available</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Passenger Details -->
        <h4 class="text-primary mb-4">Passenger Details</h4>
        <div class="row">
            <?php foreach ($passenger_details as $index => $passenger): ?>
    <?php
    $passenger_type = strtolower($passenger['passenger_type']);
    $discount_rate = $discount_rates[$passenger_type] ?? 0;
    $accom_price = $selected_accommodation['price'] ?? 0; // Default to 0 if not set
    $discounted_fare = $accom_price * (1 - $discount_rate);
    $total_cost += $discounted_fare;
    ?>
    <div class="col-md-6 mb-4">
            <div class="passenger-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Passenger <?= $index + 1 ?></h5>
                    <span class="discount-badge"><?= ucfirst(htmlspecialchars($passenger['passenger_type'])) ?></span>
                </div>
                <div class="passenger-info">
                    <p><span class="info-label">Name:</span> <?= htmlspecialchars($passenger['first_name']) ?> <?= htmlspecialchars($passenger['middle_name'] ?? '') ?> <?= htmlspecialchars($passenger['last_name']) ?></p>
                    <p><span class="info-label">Gender:</span> <?= htmlspecialchars($passenger['gender']) ?></p>
                    <p><span class="info-label">Birth Date:</span> <?= htmlspecialchars($passenger['birth_date']) ?></p>
                    <p><span class="info-label">Civil Status:</span> <?= htmlspecialchars($passenger['civil_status'] ?? 'N/A') ?></p>
                    <p><span class="info-label">Nationality:</span> <?= htmlspecialchars($passenger['nationality']) ?></p>
                    <p><span class="info-label">Address:</span> <?= htmlspecialchars($passenger['address']) ?></p>
                    <hr>
                    <div class="fare-details bg-light p-2 rounded">
                        <p class="mb-1"><span class="info-label">Discount:</span> <span class="text-danger"><?= ($discount_rate * 100) ?>%</span></p>
                        <p class="mb-0"><span class="info-label">Final Fare:</span> <span class="text-success fw-bold">₱<?= number_format($discounted_fare, 2) ?></span></p>
                    </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
        </div>

        <!-- Total Cost -->
        <div class="total-cost mt-4">
            <h4>Total Cost: <span class="text-success fw-bold">₱<?= number_format($total_cost, 2) ?></span></h4>
        </div>

        <!-- Navigation -->
        <div class="d-flex justify-content-between mt-4 action-buttons">
            <a href="passenger_details.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Edit Details
            </a>
            <a href="payment_details.php" class="btn btn-primary">
                Proceed to Payment<i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
