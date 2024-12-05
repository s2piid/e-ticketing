<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php'); // Adjust the path as needed

// Check if form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if ferry_id and accommodation are provided in the form
    if (isset($_POST['ferry_id']) && isset($_POST['accommodation'])) {
        // Store the selected ferry and accommodation in session variables
        $_SESSION['selected_ferry_id'] = $_POST['ferry_id'];
        $_SESSION['selected_accommodation_id'] = $_POST['accommodation'];

        // Optional: You can fetch more details about the selected ferry or accommodation here if needed
        // Example: Fetching ferry name for the selected ferry_id
        $ferryQuery = "SELECT ferry_name FROM ferries WHERE ferry_id = ?";
        $stmt = $conn->prepare($ferryQuery);
        $stmt->bind_param("i", $_SESSION['selected_ferry_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $ferry = $result->fetch_assoc();
        $_SESSION['selected_ferry_name'] = $ferry['ferry_name']; // Store ferry name in session

        // Redirect to passenger details page
        header("Location: passenger_details.php");
        exit();
    } else {
        // If ferry_id or accommodation is not set, redirect back to ferry selection page
        header("Location: ferry_selection.php");
        exit();
    }
} else {
    // If the request method is not POST, redirect to the ferry selection page
    header("Location: ferry_selection.php");
    exit();
}
?>
