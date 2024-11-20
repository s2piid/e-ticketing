<?php
session_start();
include('C:/xampp/htdocs/e-ticket/config.php'); // Ensure the correct path

// Check if the admin is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

// Fetch user details from the database
$user_id = $_SESSION['customer_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve booking details
    $departure = $_POST['departure'];
    $destination = $_POST['destination'];
    $departure_date = isset($_POST['departure_date']) ? $_POST['departure_date'] : ''; // Default to an empty string if not set
    $passengers = intval($_POST['passengers']);

    // Retrieve passenger details
    $firstNames = $_POST['first_name'];
    $middleNames = $_POST['middle_name'];
    $lastNames = $_POST['last_name'];
    $addresses = $_POST['address'];
    $birthdates = $_POST['birthdate'];
    $nationalities = $_POST['nationality'];
    $passengerTypes = $_POST['passenger_type'];

    // Handle file uploads and insert passenger data into the database
    foreach ($firstNames as $index => $firstName) {
        $middleName = $middleNames[$index];
        $lastName = $lastNames[$index];
        $address = $addresses[$index];
        $birthdate = $birthdates[$index];
        $nationality = $nationalities[$index];
        $passengerType = $passengerTypes[$index];

        // Handle file upload
        $validIdTmpName = $_FILES['valid_id']['tmp_name'][$index];
        $validIdName = $_FILES['valid_id']['name'][$index];
        $validIdPath = "uploads/" . basename($validIdName);

        if (!move_uploaded_file($validIdTmpName, $validIdPath)) {
            die("Failed to upload valid ID for passenger " . ($index + 1) . ".");
        }

        // Insert data into the `guest` table
        $sql = "INSERT INTO guest (booking_id, user_id, first_name, middle_name, last_name, address, birthdate, nationality, passenger_type, valid_id) 
                VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssssss", $user_id, $firstName, $middleName, $lastName, $address, $birthdate, $nationality, $passengerType, $validIdPath);

        if (!$stmt->execute()) {
            die("Error inserting details for passenger " . ($index + 1) . ": " . $stmt->error);
        }

        echo "Passenger " . ($index + 1) . " details inserted successfully.<br>";
    }

    echo "All passenger details added successfully.";
    header("Location: payment_details.php");
    exit();

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
