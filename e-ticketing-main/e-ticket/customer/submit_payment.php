<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');

// Check if customer is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

// Check if the necessary session variables are set
if (!isset($_SESSION['payment_method']) || !isset($_SESSION['total_cost']) || !isset($_SESSION['passenger_details']) || !isset($_SESSION['selected_ferry'])) {
    die("Error: Payment details are missing.");
}

$user_id = $_SESSION['customer_id'];

// Get reference number and session data
$reference_number = $_POST['reference_number'] ?? ''; // Get reference number from form
$selected_ferry = $_SESSION['selected_ferry'];
$passenger_details = $_SESSION['passenger_details'];
$departure = $_SESSION['departure'];
$destination = $_SESSION['destination'];
$departure_date = $_SESSION['departure_date'];
$total_cost = $_SESSION['total_cost'];
$payment_method = $_SESSION['payment_method'];
$status = 'confirmed'; // Set booking status as confirmed

// Prepare query to insert data into bookings table
$query = "INSERT INTO bookings 
    (fk_user_id, fk_ferry_id, departure, destination, departure_date, first_name, middle_name, last_name, gender, birth_date, civil_status, nationality, address, passenger_type, accom_price, valid_id, discount, total_cost, status, reference_number, booking_date)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($query);

// Check if prepare() was successful
if (!$stmt) {
    die("Error preparing query: " . $conn->error);
}

// Loop through passenger details and insert each one
foreach ($passenger_details as $passenger) {
    // Ensure all passenger details are available
    if (empty($passenger['first_name']) || empty($passenger['last_name']) || empty($passenger['birth_date']) || empty($passenger['passenger_type'])) {
        die("Error: Missing passenger details.");
    }

    // Bind the parameters with the respective values
    $stmt->bind_param(
        'iisssssssssssssdss', // Adjusted types for each value
        $user_id,
        $selected_ferry['ferry_id'],
        $departure,
        $destination,
        $departure_date,
        $passenger['first_name'],
        $passenger['middle_name'],
        $passenger['last_name'],
        $passenger['gender'],
        $passenger['birth_date'],
        $passenger['civil_status'],
        $passenger['nationality'],
        $passenger['address'],
        $passenger['passenger_type'],
        $selected_ferry['price'], // accom_price
        $passenger['valid_id'],
        $passenger['discount'],
        $total_cost,
        $status, // booking status
        $reference_number // reference number
    );

    // Execute the query and check if it was successful
    if (!$stmt->execute()) {
        die("Error executing query: " . $stmt->error);
    }
}

// Get the last inserted booking ID
$booking_id = $conn->insert_id;

// Save payment details
$query_payment = "INSERT INTO payments (fk_booking_id, payment_method, reference_number, amount_paid, payment_date)
                  VALUES (?, ?, ?, ?, NOW())";

$stmt_payment = $conn->prepare($query_payment);
if (!$stmt_payment) {
    die("Error preparing payment query: " . $conn->error);
}

$stmt_payment->bind_param('issd', $booking_id, $payment_method, $reference_number, $total_cost);
$stmt_payment->execute();

// Update booking status to 'confirmed'
$update_status = "UPDATE bookings SET status = 'confirmed' WHERE booking_id = ?";
$stmt_update = $conn->prepare($update_status);
if (!$stmt_update) {
    die("Error preparing update status query: " . $conn->error);
}

$stmt_update->bind_param('i', $booking_id);
$stmt_update->execute();

// Set success message in session
$_SESSION['payment_status'] = 'success';

// Redirect to payment confirmation page
header('Location: payment_confirmation.php');
exit();
?>
