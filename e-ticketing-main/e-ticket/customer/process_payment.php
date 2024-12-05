<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');

// Ensure the user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

// Validate session data
if (
    !isset($_SESSION['selected_ferry'], $_SESSION['selected_accommodation'], $_SESSION['passenger_details'], 
        $_SESSION['departure'], $_SESSION['destination'], $_SESSION['departure_date'])
) {
    die("Error: Booking details are missing. Please fill out the booking form and try again.");
}

// Ensure accommodation data is valid
$selected_accommodation = $_SESSION['selected_accommodation'];
if (empty($selected_accommodation['price']) || empty($selected_accommodation['name'])) {
    die("Error: Accommodation details are invalid or missing.");
}

// Retrieve session variables
$customer_id = $_SESSION['customer_id'];
$ferry_id = $_SESSION['selected_ferry'];
$departure = $_SESSION['departure'];
$destination = $_SESSION['destination'];
$departure_date = $_SESSION['departure_date'];
$passenger_details = $_SESSION['passenger_details'];

$accom_name = $selected_accommodation['name'];
$accom_price = $selected_accommodation['price'];

// Calculate total cost
$total_cost = 0;
$discount_rates = [
    'regular' => 0,
    'student' => 0.2,
    'pwd' => 0.2,
    'senior' => 0.2,
    'child' => 0.5,
    'infant' => 1
];

foreach ($passenger_details as $passenger) {
    $passenger_type = strtolower($passenger['passenger_type']);
    $discount_rate = $discount_rates[$passenger_type] ?? 0;
    $discounted_fare = $accom_price * (1 - $discount_rate);
    $total_cost += $discounted_fare;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reference_number'])) {
    $reference_number = $_POST['reference_number'];

    // Save booking data to database
    $stmt = $conn->prepare("
        INSERT INTO bookings (
            fk_user_id, fk_ferry_id, departure, destination, departure_date, 
            first_name, middle_name, last_name, gender, birth_date, 
            nationality, passenger_type, accom_price, discount, 
            total_cost, booking_date, status, reference_number
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?
        )
    ");

    foreach ($passenger_details as $passenger) {
        $stmt->bind_param(
            "iisssssssssddssss",
            $customer_id, $ferry_id, $departure, $destination, $departure_date,
            $passenger['first_name'], $passenger['middle_name'], $passenger['last_name'],
            $passenger['gender'], $passenger['birth_date'], $passenger['nationality'],
            $passenger['passenger_type'], $accom_price, $discount_rate, $total_cost,
            'pending', $reference_number
        );

        if (!$stmt->execute()) {
            die("Error saving passenger details: " . $stmt->error);
        }
    }

    // Clear session and confirm booking
    unset($_SESSION['selected_ferry'], $_SESSION['selected_accommodation'], $_SESSION['passenger_details']);
    $_SESSION['success'] = "Booking successfully submitted. Your reference number is: $reference_number";

    header("Location: customer_dashboard.php");
    exit();
}
?>


<!-- Step 1: Display form to input reference number -->
<form method="POST" action="process_payment.php">
    <label for="reference_number">Enter your GCash Reference Number:</label>
    <input type="text" name="reference_number" id="reference_number" required>
    <input type="submit" value="Submit">
</form>
