<?php
session_start();
include('C:/xampp/htdocs/e-ticket/config.php'); // Ensure the correct path

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Function to log admin actions with dynamic action messages
function logAdminAction($admin_id, $action, $target_id) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO Admin_Actions_Log (admin_id, action, target_id) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $admin_id, $action, $target_id);
    return $stmt->execute();
}

// Handle form submission for adding a ferry
if (isset($_POST['add_ferry'])) {
    $ferry_name = mysqli_real_escape_string($conn, $_POST['ferry_name']);
    $departure_port = mysqli_real_escape_string($conn, $_POST['departure_port']);
    $arrival_port = mysqli_real_escape_string($conn, $_POST['arrival_port']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $departure_time = $_POST['departure_time']; // e.g., "15:30"
    $arrival_time = $_POST['arrival_time']; // e.g., "03:45"

    // Insert ferry details into the database
    $stmt = $conn->prepare("INSERT INTO ferries (ferry_name) VALUES (?)");
    $stmt->bind_param("s", $ferry_name);
    if ($stmt->execute()) {
        $ferry_id = $conn->insert_id;

        // Insert ferry schedule details, using the retrieved ferry_id
        $stmt = $conn->prepare("INSERT INTO ferry_schedule (ferry_id, departure_port, arrival_port, departure_time, arrival_time, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $ferry_id, $departure_port, $arrival_port, $departure_time, $arrival_time, $status);
        if ($stmt->execute()) {
            logAdminAction($_SESSION['admin_id'], "Added new ferry: " . $ferry_name, $ferry_id);
            $success_message = "Ferry and schedule added successfully.";
        } else {
            $error_message = "Error adding ferry schedule: " . $stmt->error;
        }
    } else {
        $error_message = "Error adding ferry: " . $stmt->error;
    }
    $stmt->close();
}

// Handle form submission for adding accommodation and price
if (isset($_POST['add_accommodation'])) {
    $ferry_name = mysqli_real_escape_string($conn, $_POST['ferry_name']);
    $accom_type = mysqli_real_escape_string($conn, $_POST['accom_type']);
    $price = $_POST['price'];

    // 1. Retrieve ferry_id
    $stmt = $conn->prepare("SELECT ferry_id FROM ferries WHERE ferry_name = ?");
    $stmt->bind_param("s", $ferry_name);
    $stmt->execute();
    $stmt->bind_result($ferry_id);
    $stmt->fetch();
    $stmt->close();

    if (!$ferry_id) {
        $error_message = "Ferry not found.";
    } else {
        // Retrieve accom_id
        $stmt = $conn->prepare("SELECT accom_price_id FROM accommodation WHERE accom_type = ?");
        $stmt->bind_param("s", $accom_type);
        $stmt->execute();
        $stmt->bind_result($accom_id);
        $stmt->fetch();
        $stmt->close();

        if (!$accom_id) {
            $error_message = "Accommodation type retrieval failed.";
        } else {
            // 3. Insert price for the accommodation linked to the specific ferry
            $stmt = $conn->prepare("INSERT INTO accommodation_prices (ferry_id, accom_id, price) VALUES (?, ?, ?)");
            $stmt->bind_param("iid", $ferry_id, $accom_id, $price);
            if ($stmt->execute()) {
                $success_message = "Accommodation and price added successfully.";
                logAdminAction($_SESSION['admin_id'], "Added new accommodation price for ferry: " . $ferry_name, $ferry_id);
            } else {
                $error_message = "Error adding accommodation price: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Handle form submission for updating the ferry schedule
if (isset($_POST['update_schedule'])) {
    $ferry_name = mysqli_real_escape_string($conn, $_POST['ferry_name']);
    $departure_port = mysqli_real_escape_string($conn, $_POST['departure_port']);
    $arrival_port = mysqli_real_escape_string($conn, $_POST['arrival_port']);
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Get ferry ID based on the ferry name
    $stmt = $conn->prepare("SELECT ferry_id FROM ferries WHERE ferry_name = ?");
    $stmt->bind_param("s", $ferry_name);
    $stmt->execute();
    $stmt->bind_result($ferry_id);
    $stmt->fetch();
    $stmt->close();

    if (!$ferry_id) {
        $error_message = "Ferry not found.";
    } else {
        // Update ferry schedule
        $stmt = $conn->prepare("UPDATE ferry_schedule SET departure_port = ?, arrival_port = ?, departure_time = ?, arrival_time = ?, status = ? WHERE ferry_id = ?");
        $stmt->bind_param("sssssi", $departure_port, $arrival_port, $departure_time, $arrival_time, $status, $ferry_id);
        if ($stmt->execute()) {
            $success_message = "Schedule updated successfully.";
        } else {
            $error_message = "Error updating schedule: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch list of ferries and accommodation types
$ferries = $conn->query("SELECT ferry_id, ferry_name FROM ferries ORDER BY ferry_name");
$accommodation_types = $conn->query("SELECT * FROM accommodation");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Ferries</title>
<link rel="stylesheet" href="C:/xampp/htdocs/e-ticket/style.css">
<style>
/* General Body Styling */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f0f2f5;
    margin: 0;
    padding: 0;
    color: #333;
}

/* Container Styling */
.container {
    width: 85%;
    max-width: 900px;
    margin: 20px auto;
    padding: 20px;
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    border-radius: 12px;
}

/* Header Styling */
h2 {
    text-align: center;
    color: #4a4a4a;
    margin-bottom: 30px;
    font-size: 28px;
    letter-spacing: 1px;
}

h3 {
    color: #5c636a;
    margin-bottom: 20px;
    font-size: 22px;
}

/* Tab Navigation */
.tabs {
    display: flex;
    justify-content: space-around;
    margin-bottom: 20px;
}

.tabs button {
    background-color: #007bff;
    color: white;
    padding: 14px 20px;
    font-size: 18px;
    border: none;
    cursor: pointer;
    border-radius: 6px;
    transition: background-color 0.3s;
    flex: 1;
    text-align: center;
}

.tabs button:hover {
    background-color: #0056b3;
}

/* Active Tab Styling */
.tabs button.active {
    background-color: #0056b3;
}

/* Content Section Styling */
.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

/* Form Styling */
form {
    background-color: #f7f9fc;
    padding: 25px;
    border-radius: 10px;
}

.input-group {
    margin-bottom: 15px;
}

.input-group label {
    font-size: 18px;
    margin-bottom: 5px;
    display: block;
}

.input-group input,
.input-group select {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 6px;
}

/* Button Styling */
button {
    background-color: #28a745;
    color: white;
    padding: 12px 20px;
    font-size: 18px;
    border: none;
    cursor: pointer;
    border-radius: 8px;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #218838;
}

/* Success and Error Messages */
.success-message,
.error-message {
    margin-top: 20px;
    padding: 10px;
    border-radius: 6px;
    text-align: center;
}

.success-message {
    background-color: #d4edda;
    color: #155724;
}

.error-message {
    background-color: #f8d7da;
    color: #721c24;
}
</style>
</head>
<body>
<div class="container">
    <h2>Manage Ferries and Schedules</h2>
    <!-- Tab Navigation -->
    <div class="tabs">
        <button class="active" onclick="showTab('addFerry')">Add Ferry</button>
        <button onclick="showTab('addAccommodation')">Add Accommodation</button>
        <button onclick="showTab('updateSchedule')">Update Schedule</button>
    </div>

    <!-- Add Ferry Form -->
    <div id="addFerry" class="tab-content active">
        <h3>Add New Ferry</h3>
        <?php if (isset($success_message)) { echo "<div class='success-message'>$success_message</div>"; } ?>
        <?php if (isset($error_message)) { echo "<div class='error-message'>$error_message</div>"; } ?>
        <form method="POST" action="">
            <div class="input-group">
                <label for="ferry_name">Ferry Name</label>
                <input type="text" name="ferry_name" id="ferry_name" required>
            </div>
            <div class="input-group">
                <label for="departure_port">Departure Port</label>
                <input type="text" name="departure_port" id="departure_port" required>
            </div>
            <div class="input-group">
                <label for="arrival_port">Arrival Port</label>
                <input type="text" name="arrival_port" id="arrival_port" required>
            </div>
            <div class="input-group">
                <label for="departure_time">Departure Time</label>
                <input type="time" name="departure_time" id="departure_time" required>
            </div>
            <div class="input-group">
                <label for="arrival_time">Arrival Time</label>
                <input type="time" name="arrival_time" id="arrival_time" required>
            </div>
            <div class="input-group">
                <label for="status">Status</label>
                <select name="status" id="status" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" name="add_ferry">Add Ferry</button>
        </form>
    </div>

    <!-- Add Accommodation Form -->
    <div id="addAccommodation" class="tab-content">
        <h3>Add Accommodation</h3>
        <!-- Form goes here, similar structure as Add Ferry -->
    </div>

    <!-- Update Schedule Form -->
    <div id="updateSchedule" class="tab-content">
        <h3>Update Ferry Schedule</h3>
        <!-- Form goes here, similar structure as Add Ferry -->
    </div>

    <input type="hidden" name="active_tab" value="addFerry">
</div>
<script>
function showTab(tabId) {
    const tabs = document.querySelectorAll('.tab-content');
    const buttons = document.querySelectorAll('.tabs button');

    tabs.forEach(tab => tab.classList.remove('active'));
    buttons.forEach(button => button.classList.remove('active'));

    document.getElementById(tabId).classList.add('active');
    document.querySelector(`button[onclick="showTab('${tabId}')"]`).classList.add('active');
    
    // Update active tab
    document.querySelector("input[name='active_tab']").value = tabId;
}
</script>
</body>
</html>
