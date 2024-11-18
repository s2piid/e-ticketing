<?php
session_start();
include('C:/xampp/htdocs/e-ticket/config.php'); // Ensure the correct path

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
// Function to log admin actions
function logAddFerry($admin_id, $action, $target_id) {
    global $conn;
    $action = 'add new ferry';
    // Prepare and execute the log insert query
    $stmt = $conn->prepare("INSERT INTO Admin_Actions_Log (admin_id, action, target_id) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $admin_id, $action, $target_id);
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

// Handle form submission for adding a ferry
if (isset($_POST['add_ferry'])) {
    $ferry_name = $_POST['ferry_name'];
    $departure_port = $_POST['departure_port'];
    $arrival_port = $_POST['arrival_port'];
    $status = $_POST['status'];

    // Directly get time in 24-hour format
    $departure_time = $_POST['departure_time']; // e.g., "15:30"
    $arrival_time = $_POST['arrival_time']; // e.g., "03:45"

    // Insert ferry details into the database
    $stmt = $conn->prepare("INSERT INTO ferries (ferry_name) VALUES (?)");
    $stmt->bind_param("s", $ferry_name);
    if ($stmt->execute()) {
        // Get the ferry_id of the newly inserted ferry
        $ferry_id = $conn->insert_id;

        // Insert ferry schedule details, using the retrieved ferry_id
        $stmt = $conn->prepare("INSERT INTO ferry_schedule (ferry_id, departure_port, arrival_port, departure_time, arrival_time, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $ferry_id, $departure_port, $arrival_port, $departure_time, $arrival_time, $status);
        if ($stmt->execute()) {
            // Log the admin action
            logAddFerry($_SESSION['admin_id'], 'add new ferry', $ferry_id);
            $success_message = "Ferry and schedule added successfully.";
        } else {
            $error_message = "Error adding ferry schedule: " . $stmt->error;
        }
    } else {
        $error_message = "Error adding ferry: " . $stmt->error;
    }
    $stmt->close();
}
//Admin Log
function logAddAccommodation($admin_id, $action, $target_id) {
    global $conn;
    $action = 'add accomodation aype';
    // Prepare and execute the log insert query
    $stmt = $conn->prepare("INSERT INTO Admin_Actions_Log (admin_id, action, target_id) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $admin_id, $action, $target_id);
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}
//Admin Log
function logAddAccommodationPrice($admin_id, $action, $target_id) {
    global $conn;
    $action = 'add new ferry';
    // Prepare and execute the log insert query
    $stmt = $conn->prepare("INSERT INTO Admin_Actions_Log (admin_id, action, target_id) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $admin_id, $action, $target_id);
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

// Handle form submission for adding accommodation and price
if (isset($_POST['add_accommodation'])) {
    $ferry_name = $_POST['ferry_name']; // Fetching ferry name from the dropdown
    $accom_type = $_POST['accom_type'];
    $price = $_POST['price'];

    // 1. Retrieve ferry_id based on the ferry name
    $stmt = $conn->prepare("SELECT ferry_id FROM ferries WHERE ferry_name = ?");
    $stmt->bind_param("s", $ferry_name);
    $stmt->execute();
    $stmt->bind_result($ferry_id);
    $stmt->fetch();
    $stmt->close();

    // Check if ferry_id was retrieved successfully
    if (!$ferry_id) {
        $error_message = "Ferry not found.";
    } else {
        // 2. Insert new accommodation type if it doesn't exist
        $stmt = $conn->prepare("INSERT INTO accommodation (accom_type) VALUES (?) ON DUPLICATE KEY UPDATE accom_type = accom_type");
        $stmt->bind_param("s", $accom_type);
        $stmt->execute();
        logAddAccommodation($_SESSION['admin_id'], 'add new ferry', $ferry_id);
        // Get the accommodation ID (accom_id)
        $accom_id = $conn->insert_id;

        // 3. Insert price for the accommodation linked to the specific ferry
        $stmt = $conn->prepare("INSERT INTO accommodation_prices (ferry_id, accom_id, price) VALUES (?, ?, ?)");
        $stmt->bind_param("iid", $ferry_id, $accom_id, $price);
        logAddAccommodationPrice($_SESSION['admin_id'], 'add new ferry', $ferry_id);
        if ($stmt->execute()) {
            $success_message = "Accommodation and price added successfully.";
        } else {
            $error_message = "Error adding accommodation: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch list of ferries for displaying in dropdown
$ferries = $conn->query("SELECT ferry_id, ferry_name FROM ferries ORDER BY ferry_name");

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
    font-family: Arial, sans-serif;
    background-color: #f8f9fa;
    margin: 0;
    padding: 0;
}

.container {
    width: 80%;
    margin: 20px auto;
    padding: 20px;
    background-color: white;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

h2 {
    text-align: center;
    color: #343a40;
    margin-bottom: 30px;
}

h3 {
    color: #495057;
    margin-bottom: 15px;
}

/* Success and Error Messages */
.success-message {
    color: #28a745;
    background-color: #d4edda;
    padding: 10px;
    border-radius: 5px;
    text-align: center;
    margin-bottom: 20px;
}

.error-message {
    color: #dc3545;
    background-color: #f8d7da;
    padding: 10px;
    border-radius: 5px;
    text-align: center;
    margin-bottom: 20px;
}

/* Input Groups */
.input-group {
    margin-bottom: 15px;
}

.input-group input,
.input-group select {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
}

.input-group select {
    cursor: pointer;
}

/* Form Buttons */
.btn {
    background-color: #007bff;
    color: white;
    padding: 12px 20px;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
    transition: background-color 0.3s ease;
}

.btn:hover {
    background-color: #0056b3;
}

/* Forms Styling */
form {
    background-color: #f2f2f2;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
}

form h3 {
    margin-top: 0;
}

form .input-group {
    margin-bottom: 20px;
}

/* Table Styling for displaying ferries */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 30px;
}

table th, table td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: left;
}

table th {
    background-color: #f8f9fa;
    color: #495057;
}

table tbody tr:nth-child(even) {
    background-color: #f1f1f1;
}

table tbody tr:hover {
    background-color: #e2e6ea;
}

/* Responsive Styling */
@media (max-width: 768px) {
    .container {
        width: 95%;
        padding: 15px;
    }

    .input-group input,
    .input-group select {
        font-size: 14px;
        padding: 8px;
    }

    .btn {
        font-size: 14px;
        padding: 10px;
    }
}

</style>
</head>
<body>
<div class="container">
    <h2>Manage Ferries</h2>

    <?php if (isset($success_message)): ?>
        <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>

<!-- Form to Add a Ferry -->
<form method="POST" action="">
    <h3>Add Ferry</h3>
    <div class="input-group">
        <input type="text" name="ferry_name" placeholder="Ferry Name" required>
    </div>
    <div class="input-group">
        <input type="text" name="departure_port" placeholder="Departure Port" required>
    </div>
    <div class="input-group">
        <input type="text" name="arrival_port" placeholder="Arrival Port" required>
    </div>
    <div class="input-group">
        <label for="departure_time">Departure Time</label>
        <input type="time" id="departure_time" name="departure_time" required>
    </div>

    <div class="input-group">
        <label for="arrival_time">Arrival Time</label>
        <input type="time" id="arrival_time" name="arrival_time" required>
    </div>

    <div class="input-group">
        <select name="status" required>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>
    <button type="submit" name="add_ferry" class="btn">Add Ferry</button>
</form>

<!-- Form to Add Accommodation and Price -->
<form method="POST" action="">
    <h3>Add Accommodation Type and Price</h3>
    <div class="input-group">
        <select name="ferry_name" required>
            <option value="">Select Ferry</option>
            <?php 
            // Fetching ferries for the accommodation form
            while ($row = $ferries->fetch_assoc()): ?>
                <option value="<?php echo $row['ferry_name']; ?>"><?php echo $row['ferry_name']; ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="input-group">
        <input type="text" name="accom_type" placeholder="Accommodation Type (e.g., Standard, VIP)" required>
    </div>
    <div class="input-group">
        <input type="number" step="0.01" name="price" placeholder="Price" required>
    </div>
    <button type="submit" name="add_accommodation" class="btn">Add Accommodation</button>
</form>

</div>
</body>
</html>
