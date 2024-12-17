<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');
// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";
// Ensure the user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

if (!isset(
    $_SESSION['selected_ferry'],
    $_SESSION['selected_accommodation'],
    $_SESSION['passenger_details'],
    $_SESSION['departure'],
    $_SESSION['destination'],
    $_SESSION['departure_date']
)) {
    die("Error: Booking details are missing. Please try again.");
}

// Retrieve session data
$passenger_details = $_SESSION['passenger_details'];
$customer_id = (int)$_SESSION['customer_id'];
$ferry_id = (int)$_SESSION['selected_ferry'];
$departure = $_SESSION['departure'];
$destination = $_SESSION['destination'];
$departure_date = $_SESSION['departure_date'];
$accom_price = (float)$_SESSION['selected_accommodation']['price'];

// Discount rates
$discount_rates = [
    'regular' => 0,
    'student' => 0.2,
    'pwd' => 0.2,
    'senior' => 0.2,
    'child' => 0.5,
    'infant' => 1
];

$error_message = $success_message = '';

// Ensure the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reference_number'])) {
    $reference_number = trim($_POST['reference_number']);

    // Validate reference number
    if (!preg_match('/^\d{13}$/', $reference_number)) {
        $error_message = "Invalid GCash reference number. It must be 13 digits.";
    } else {
        // Check if reference number is unique
        $check_ref = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE reference_number = ?");
        $check_ref->bind_param("s", $reference_number);
        $check_ref->execute();
        $check_ref->bind_result($count);
        $check_ref->fetch();
        $check_ref->close();

        if ($count > 0) {
            $error_message = "This reference number is already used.";
        } else {
            // Calculate total cost and insert booking data
           // Prepare the SQL statement
$stmt = $conn->prepare("
INSERT INTO bookings (
    fk_user_id, fk_ferry_id, departure, destination, departure_date,
    first_name, middle_name, last_name, gender, birth_date, 
    civil_status, nationality, address, passenger_type, 
    accom_price, valid_id, discount, total_cost, status, 
    reference_number, ticket_number
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

if (!$stmt) {
throw new Exception("Failed to prepare the SQL statement: " . $conn->error);
}

$status = 'pending';
$total_cost = 0;
$ticket_number = 'TICKET-' . strtoupper(bin2hex(random_bytes(5)));

// Loop through passenger details
foreach ($passenger_details as $passenger) {
// Calculate discount and total cost
$discount_rate = ($discount_rates[strtolower($passenger['passenger_type'])] ?? 0);
$passenger_total_cost = $accom_price * (1 - $discount_rate);
$total_cost += $passenger_total_cost;

// Assign the parameters to variables
$first_name = $passenger['first_name'];
$middle_name = $passenger['middle_name'];
$last_name = $passenger['last_name'];
$gender = $passenger['gender'];
$birth_date = $passenger['birth_date'];
$civil_status = $passenger['civil_status'];
$nationality = $passenger['nationality'];
$address = $passenger['address'];
$passenger_type = $passenger['passenger_type'];
$valid_id = $passenger['id_file'] ?? null;
$accommodation_price = $accom_price;

// Bind parameters for each passenger
$stmt->bind_param(
    "iisssssssssssssdsssss",
    $customer_id,        // fk_user_id
    $ferry_id,           // fk_ferry_id
    $departure,          // departure
    $destination,        // destination
    $departure_date,     // departure_date
    $first_name,         // first_name
    $middle_name,        // middle_name
    $last_name,          // last_name
    $gender,             // gender
    $birth_date,         // birth_date
    $civil_status,       // civil_status
    $nationality,        // nationality
    $address,            // address
    $passenger_type,     // passenger_type
    $accommodation_price,// accom_price
    $valid_id,           // valid_id
    $discount_rate,      // discount
    $passenger_total_cost, // total_cost
    $status,             // status
    $reference_number,   // reference_number
    $ticket_number       // ticket_number
);

if (!$stmt->execute()) {
    throw new Exception("Insert failed: " . $stmt->error);
}
}

$conn->commit();
unset($_SESSION['selected_ferry'], $_SESSION['selected_accommodation'], $_SESSION['passenger_details']);
$success_message = "Booking successfully submitted! Please check your booking history.";

        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <?php if ($error_message): ?>
        <div class="modal fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Error</h5>
                    </div>
                    <div class="modal-body">
                        <p><?= htmlspecialchars($error_message); ?></p>
                    </div>
                    <div class="modal-footer">
                        <a href="payment_details.php" class="btn btn-secondary">Go Back</a>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif ($success_message): ?>
        <div class="modal fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Booking Success</h5>
                    </div>
                    <div class="modal-body">
                        <p><?= htmlspecialchars($success_message); ?></p>
                    </div>
                    <div class="modal-footer">
                        <a href="customer_dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
