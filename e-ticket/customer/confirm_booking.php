<?php
session_start();
include('C:/xampp/htdocs/e-ticket/config.php');

// Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

// Ensure form data is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input data
    $customer_id = $_SESSION['customer_id'];
    $ferry_id = $_POST['ferry_id'];
    $discount_type = $_POST['discount_type'];
    $passenger_name = htmlspecialchars($_POST['passenger_name']);
    $contact = htmlspecialchars($_POST['contact']);
    $num_tickets = intval($_POST['num_tickets']);

    // Fetch the selected ferry schedule and accommodation price
    $price_query = "
        SELECT ap.price, a.accom_type
        FROM accommodation_prices ap
        LEFT JOIN accommodation a ON ap.accom_id = a.accom_price_id
        WHERE ap.ferry_id = ?
    ";

    $stmt = $conn->prepare($price_query);
    $stmt->bind_param("i", $ferry_id);
    $stmt->execute();
    $price_result = $stmt->get_result();
    $price_row = $price_result->fetch_assoc();

    $ticket_price = floatval($price_row['price']);
    $accommodation_type = $price_row['accom_type'];

    // Calculate subtotal
    $subtotal = $ticket_price * $num_tickets;
    $discount = 0;

    // Apply discount based on the selected discount type
    switch ($discount_type) {
        case 'student':
            $discount = $subtotal * 0.20; // 20% discount for students
            break;
        case 'senior':
            $discount = $subtotal * 0.30; // 30% discount for senior citizens
            break;
        case 'pwd':
            $discount = $subtotal * 0.20; // 20% discount for PWD
            break;
        default:
            $discount = 0; // No discount for regular customers
            break;
    }

    // Calculate total cost
    $total_cost = $subtotal - $discount;

    // Set booking status to 'confirmed'
    $status = 'pending';

    // Insert the booking into the 'bookings' table
    $booking_query = "
        INSERT INTO bookings (fk_user_id, fk_ferry_id, booking_date, status, sub_price, discount_type, discount, total_cost)
        VALUES (?, ?, NOW(), ?, ?, ?, ?, ?)
    ";

    $stmt = $conn->prepare($booking_query);
    $stmt->bind_param(
        "iisssdd",
        $customer_id,
        $ferry_id,
        $status,
        $subtotal,
        $discount_type,
        $discount,
        $total_cost
    );

    // Execute the booking query
    if ($stmt->execute()) {
        // Fetch the newly created booking ID
        $booking_id = $stmt->insert_id;
        echo "<h2>Booking Pending!</h2>";
        echo "<p>Booking ID: <strong>{$booking_id}</strong></p>";
        echo "<p>Passenger Name: <strong>{$passenger_name}</strong></p>";
        echo "<p>Contact Number: <strong>{$contact}</strong></p>";
        echo "<p>Accommodation: <strong>{$accommodation_type}</strong></p>";
        echo "<p>Number of Tickets: <strong>{$num_tickets}</strong></p>";
        echo "<p>Total Cost: <strong>₱" . number_format($total_cost, 2) . "</strong></p>";
        echo "<a href='dashboard.php'>Back to Dashboard</a>";
    } else {
        // If booking fails, display an error message
        echo "<h2>Error!</h2>";
        echo "<p>Unable to complete the booking. Please try again.</p>";
        echo "<a href='book_trip.php'>Try Again</a>";
    }

    // Close the prepared statement
    $stmt->close();
} else {
    // Redirect if the script is accessed directly without form submission
    header("Location: book_trip.php");
    exit();
}

// Close the database connection
$conn->close();
?>
