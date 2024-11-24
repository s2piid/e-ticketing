<?php
session_start();
include('C:/xampp/htdocs/e-ticket/config.php'); // Ensure the correct path

// Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

// Fetch the total fare, accommodation price, and discount rate from the session
$total_fare = isset($_SESSION['total_fare']) ? $_SESSION['total_fare'] : 0;
$accom_price = isset($_SESSION['accom_price']) ? $_SESSION['accom_price'] : 0;
$discount = isset($_SESSION['discount']) ? $_SESSION['discount'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Details</title>
    <!-- Include Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
        }
        .form-label {
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        #credit-card-info {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card p-4">
            <h2 class="text-center mb-4">Payment Details</h2>

            <!-- Display Total Fare and Discount -->
            <div class="mb-4">
                <p><strong>Total Fare for All Passengers:</strong> $<?= number_format($total_fare, 2) ?></p>
                <p><strong>Accommodation Price:</strong> $<?= number_format($accom_price, 2) ?></p>
                <p><strong>Discount Applied:</strong> <?= $discount * 100 ?>%</p>
            </div>

            <form action="process_payment.php" method="POST">
                <!-- Payment Method Selection -->
                <div class="mb-4">
                    <label for="payment_method" class="form-label">Select Payment Method</label>
                    <select name="payment_method" id="payment_method" class="form-select" required>
                        <option value="" disabled selected>Select Payment Method</option>
                        <option value="cash">Cash</option>
                        <option value="gcash">GCash</option>
                        <option value="credit_card">Credit Card</option>
                    </select>
                </div>

                <!-- Credit Card Information (shown only if Credit Card is selected) -->
                <div id="credit-card-info">
                    <div class="mb-4">
                        <label for="card_number" class="form-label">Card Number</label>
                        <input type="text" name="card_number" id="card_number" class="form-control" placeholder="Enter card number" required>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="expiry_date" class="form-label">Expiry Date</label>
                            <input type="month" name="expiry_date" id="expiry_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="cvv" class="form-label">CVV</label>
                            <input type="text" name="cvv" id="cvv" class="form-control" placeholder="Enter CVV" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="billing_address" class="form-label">Billing Address</label>
                        <input type="text" name="billing_address" id="billing_address" class="form-control" placeholder="Enter billing address" required>
                    </div>

                    <div class="mb-4">
                        <label for="billing_zip" class="form-label">Billing Zip Code</label>
                        <input type="text" name="billing_zip" id="billing_zip" class="form-control" placeholder="Enter billing zip code" required>
                    </div>
                </div>

                <!-- Hidden Inputs for Booking Data -->
                <input type="hidden" name="total_fare" value="<?= htmlspecialchars($total_fare) ?>">
                <input type="hidden" name="accom_price" value="<?= htmlspecialchars($accom_price) ?>">
                <input type="hidden" name="discount" value="<?= htmlspecialchars($discount) ?>">

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Submit Payment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('payment_method').addEventListener('change', function() {
            var paymentMethod = this.value;
            var creditCardInfo = document.getElementById('credit-card-info');

            // Show/hide credit card details based on payment method
            if (paymentMethod === 'credit_card') {
                creditCardInfo.style.display = 'block';
            } else {
                creditCardInfo.style.display = 'none';
            }
        });
    </script>
</body>
</html>
