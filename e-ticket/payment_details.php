<?php
session_start();
include('C:/xampp/htdocs/e-ticket/config.php'); // Ensure the correct path

// Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

// Fetch booking details from session or database
$user_id = $_SESSION['customer_id'];

// You might need to fetch other booking data like the selected ferry and passengers
// For now, let's assume you have a booking_id passed from the previous page or session
$booking_id = $_SESSION['booking_id']; // Retrieve this from the session or database

// You can also fetch details from the database if needed (for confirmation)
// Example: $sql = "SELECT * FROM bookings WHERE booking_id = ?"; 
// $stmt = $conn->prepare($sql);
// $stmt->bind_param('i', $booking_id);
// $stmt->execute();
// $result = $stmt->get_result();
// $booking_details = $result->fetch_assoc();

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
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card p-4">
            <h2 class="text-center mb-4">Payment Details</h2>

            <!-- Display Booking Information -->
            <div class="mb-4">
                <p><strong>Booking ID:</strong> <?= htmlspecialchars($booking_id) ?></p>
                <!-- Add other booking details like ferry and passenger info if necessary -->
            </div>

            <form action="process_payment.php" method="POST">
                <!-- Payment Information -->
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

                <!-- Hidden Inputs for Booking Data -->
                <input type="hidden" name="booking_id" value="<?= htmlspecialchars($booking_id) ?>">

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Submit Payment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
