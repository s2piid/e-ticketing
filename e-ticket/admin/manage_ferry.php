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
    $action = 'add accomodation type';
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
    $action = 'add new Accommodation Price';
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
    $ferry_name = $_POST['ferry_name'];
    $accom_type = $_POST['accom_type'];
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
        // 2. Insert new accommodation type if it doesn't exist
        $stmt = $conn->prepare("INSERT INTO accommodation (accom_type) VALUES (?) ON DUPLICATE KEY UPDATE accom_type = accom_type");
        $stmt->bind_param("s", $accom_type);
        $stmt->execute();
        $stmt->close();

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
                logAddAccommodationPrice($_SESSION['admin_id'], 'add new Accomodation Price', $ferry_id);
            } else {
                $error_message = "Error adding accommodation: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}


// Fetch list of ferries for displaying in dropdown
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

/* Success and Error Messages */
.success-message {
    color: #155724;
    background-color: #d4edda;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #c3e6cb;
    text-align: center;
    margin-bottom: 20px;
}

.error-message {
    color: #721c24;
    background-color: #f8d7da;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #f5c6cb;
    text-align: center;
    margin-bottom: 20px;
}

/* Input Groups */
.input-group {
    margin-bottom: 20px;
}

.input-group input,
.input-group select {
    width: 100%;
    padding: 12px;
    font-size: 16px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    box-sizing: border-box;
    transition: border-color 0.2s;
}

.input-group input:focus,
.input-group select:focus {
    border-color: #007bff;
    outline: none;
}

.input-group select {
    cursor: pointer;
    background-color: #fff;
}

/* Button Styling */
.btn {
    background-color: #007bff;
    color: #fff;
    padding: 14px;
    font-size: 18px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    width: 100%;
    transition: background-color 0.3s ease, transform 0.2s;
}

.btn:hover {
    background-color: #0056b3;
    transform: scale(1.02);
}

/* Form Styling */
form {
    background-color: #f7f9fc;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

/* Table Styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 30px;
    border-radius: 8px;
    overflow: hidden;
}

table th, table td {
    padding: 15px;
    border: 1px solid #dee2e6;
    text-align: left;
    font-size: 16px;
}

table th {
    background-color: #e9ecef;
    color: #343a40;
    font-weight: bold;
}

table tbody tr:nth-child(even) {
    background-color: #f8f9fa;
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
        padding: 10px;
    }

    .btn {
        font-size: 16px;
        padding: 12px;
    }
}

/* Navigation Menu Styling */
.menu {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
    gap: 15px;
}

.menu a,
.btn-logout {
    padding: 12px 20px;
    background: #17a2b8;
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
    border: none;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.menu a:hover,
.btn-logout:hover {
    background: #138496;
}

/* Logout Button Specific */
.btn-logout {
    background: #dc3545;
}

.btn-logout:hover {
    background: #c82333;
}


</style>
</head>
<body>


<script>
    function logout() {
        if (confirm("Are you sure you want to log out?")) {
            window.location.href = 'admin_login.php';
        }
    }
</script>
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
        <label for="ferry_name">Ferry Name</label>
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
        <label for="accom_type">Accommodation Type</label>
        <select name="accom_type" required>
            <option value="">Select Accommodation Type</option>
            <?php 
            // Fetching accommodation types for the dropdown
            
            while ($row = $accommodation_types->fetch_assoc()): ?>
                <option value="<?php echo $row['accom_type']; ?>"><?php echo $row['accom_type']; ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="input-group">
        <label for="price">Price</label>
        <input type="number" step="0.01" name="price" placeholder="Enter Price" required>
    </div>

    <div class="input-group">
        <button type="submit" name="add_accommodation" class="btn">Add Accommodation</button>
    </div>
</form>

</body>
</html>
