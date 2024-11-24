<?php
session_start();
include('C:/xampp/htdocs/e-ticket/config.php'); // Ensure the correct path

$discount_rates = [
    'regular' => 0,    
    'student' => 0.1,  
    'senior' => 0.2,   
    'pwd' => 0.15,     
    'child' => 0.5,    
    'infant' => 0.8    
];

if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

$user_id = $_SESSION['customer_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $departure = $_POST['departure'];
    $destination = $_POST['destination'];
    $departure_date = $_POST['departure_date'] ?? '';
    $passengers = intval($_POST['passengers']);
    $ferry_id = $_POST['ferry_id'] ?? '';
    $accom_id = $_POST['accom_id'] ?? '';

    if (empty($ferry_id) || empty($accom_id)) {
        die("Error: Ferry or Accommodation not selected.");
    }

    $accom_query = "SELECT price FROM accommodation_prices WHERE accom_id = ? AND ferry_id = ?";
    $stmt_accom = $conn->prepare($accom_query);
    $stmt_accom->bind_param("ii", $accom_id, $ferry_id);
    $stmt_accom->execute();
    $result_accom = $stmt_accom->get_result();
    $stmt_accom->close();

    $accom_price = $result_accom->num_rows > 0 ? $result_accom->fetch_assoc()['price'] : 0;
    $total_fare = 0;

    foreach ($_POST['first_name'] as $index => $first_name) {
        $last_name = $_POST['last_name'][$index];
        $address = $_POST['address'][$index];
        $birth_date = $_POST['birth_date'][$index];
        $passenger_type = strtolower($_POST['passenger_type'][$index]);
        $discount = $discount_rates[$passenger_type] ?? 0;
        $base_fare = 100; // Adjust as needed
        $discounted_fare = $base_fare * (1 - $discount);
        $total_fare += $discounted_fare + $accom_price;

        $stmt = $conn->prepare("INSERT INTO bookings (fk_user_id, fk_ferry_id, first_name, last_name, address, birth_date, passenger_type, accom_price) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssssd", $user_id, $ferry_id, $first_name, $last_name, $address, $birth_date, $passenger_type, $accom_price);
        $stmt->execute();
        $stmt->close();
    }

    $_SESSION['total_fare'] = $total_fare;
    header("Location: payment_details.php");
    exit();
}
?>
