<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');

// Define discount rates
$discount_rates = [
    'regular' => 0,
    'student' => 0.1,
    'senior' => 0.2,
    'pwd' => 0.15,
    'child' => 0.5,
    'infant' => 1
];

// Check if customer is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

$user_id = $_SESSION['customer_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Required inputs with fallback for missing values
    $departure = $_POST['departure'] ?? '';  // Fallback to empty string if not set
    $destination = $_POST['destination'] ?? '';  // Fallback to empty string if not set
    $departure_date = $_POST['departure_date'] ?? '';  // Fallback to empty string if not set
    $passengers = intval($_POST['passengers'] ?? 0);  // Fallback to 0 if not set
    $ferry_id = $_POST['ferry_id'] ?? '';  // Fallback to empty string if not set
    $accom_id = $_POST['accom_id'] ?? '';  // Fallback to empty string if not set

    // Validate ferry and accommodation selection
    if (empty($ferry_id) || empty($accom_id)) {
        die("Error: Ferry or Accommodation not selected.");
    }

    // Fetch ferry name from ferries table
    $ferry_query = "SELECT ferry_name FROM ferries WHERE ferry_id = ?";
    $stmt_ferry = $conn->prepare($ferry_query);
    $stmt_ferry->bind_param("i", $ferry_id);
    $stmt_ferry->execute();
    $result_ferry = $stmt_ferry->get_result();
    $stmt_ferry->close();

    if ($result_ferry->num_rows > 0) {
        $ferry_name = $result_ferry->fetch_assoc()['ferry_name'];
    } else {
        die("Error: Ferry details not found.");
    }

    // Fetch accommodation price from database
    $accom_query = "SELECT price FROM accommodation_prices WHERE accom_id = ? AND ferry_id = ?";
    $stmt_accom = $conn->prepare($accom_query);
    $stmt_accom->bind_param("ii", $accom_id, $ferry_id);
    $stmt_accom->execute();
    $result_accom = $stmt_accom->get_result();
    $stmt_accom->close();

    if ($result_accom->num_rows > 0) {
        $accom_price = $result_accom->fetch_assoc()['price'];
    } else {
        die("Error: Accommodation price not found.");
    }

    $total_fare = 0;
    $total_discount = 0;

    $booking_details = [];

    // Calculate total fare and insert each passenger's details into the database
    foreach ($_POST['first_name'] as $index => $first_name) {
        $middle_name = $_POST['middle_name'][$index] ?? '';  // Fallback for missing middle name
        $last_name = $_POST['last_name'][$index] ?? '';  // Fallback for missing last name
        $gender = $_POST['gender'][$index] ?? '';  // Fallback for missing gender
        $civil_status = $_POST['civil_status'][$index] ?? '';  // Fallback for missing civil status
        $nationality = $_POST['nationality'][$index] ?? '';  // Fallback for missing nationality
        $address = $_POST['address'][$index] ?? '';  // Fallback for missing address
        $birth_date = $_POST['birth_date'][$index] ?? '';  // Fallback for missing birth date
        $passenger_type = strtolower($_POST['passenger_type'][$index] ?? 'regular');  // Default to 'regular' if missing
        $valid_id = $_POST['valid_id'][$index] ?? '';  // Fallback for missing valid ID

        // Validate necessary inputs
        if (empty($first_name) || empty($last_name) || empty($birth_date) || empty($passenger_type)) {
            die("Error: Missing passenger details.");
        }

        // Calculate discounted fare
        $discount = $discount_rates[$passenger_type] ?? 0;
        $discounted_fare = $accom_price * (1 - $discount);
        $total_fare += $discounted_fare;
        $total_discount += $accom_price * $discount;

        // Insert passenger details into the database
$stmt = $conn->prepare(
    "INSERT INTO bookings 
        (fk_user_id, fk_ferry_id, departure, destination, departure_date, first_name, middle_name, last_name, gender, civil_status, nationality, address, birth_date, passenger_type, accom_price, valid_id, discount, total_cost, booking_date, status) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'pending')"
);

$total_cost = $discounted_fare; // Total fare after discount
$stmt->bind_param(
    "iisssssssssssdids",  // i: integer, s: string, d: double
    $user_id, 
    $ferry_id, 
    $departure, 
    $destination, 
    $departure_date, 
    $first_name, 
    $middle_name, 
    $last_name, 
    $gender, 
    $civil_status, 
    $nationality, 
    $address, 
    $birth_date, 
    $passenger_type, 
    $accom_price, 
    $valid_id,   
    $discount,
    $total_cost,
    $status  // Add this for the 'status' column
);

// Set 'pending' as the value for status
$status = 'pending';

$stmt->execute();
$stmt->close();

        // Store booking details for session
        $booking_details[] = [
            'first_name' => $first_name,
            'middle_name' => $middle_name,
            'last_name' => $last_name,
            'gender' => $gender,
            'civil_status' => $civil_status,
            'nationality' => $nationality,
            'address' => $address,
            'birth_date' => $birth_date,
            'passenger_type' => $passenger_type,
            'accom_price' => $accom_price,
            'discounted_fare' => $discounted_fare
        ];
    }

    // Store all relevant booking details in session, including ferry name
    $_SESSION['booking_details'] = [
        'ferry_name' => $ferry_name,  // Added ferry name here
        'departure' => $departure,
        'destination' => $destination,
        'departure_date' => $departure_date,
        'passengers' => $booking_details
    ];
    $_SESSION['total_fare'] = $total_fare;
    $_SESSION['total_discount'] = $total_discount;
    $_SESSION['accom_price'] = $accom_price;

    // Redirect to the payment details page
    header("Location: payment_details.php");
    exit();
}
?>
