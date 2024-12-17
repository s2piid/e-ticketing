<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');
include 'header.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// General logging function
function logAdminAction($admin_id, $action, $target_id) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO Admin_Actions_Log (admin_id, action, target_id) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $admin_id, $action, $target_id);
    return $stmt->execute();
}

// Handle form submission for adding a ferry
if (isset($_POST['add_ferry'])) {
    $ferry_name = $_POST['ferry_name'];
    $departure_port = $_POST['departure_port'];
    $arrival_port = $_POST['arrival_port'];
    $status = $_POST['status'];
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];

    // Insert ferry details into the database
    $stmt = $conn->prepare("INSERT INTO ferries (ferry_name) VALUES (?)");
    $stmt->bind_param("s", $ferry_name);
    
    if ($stmt->execute()) {
        $ferry_id = $conn->insert_id;

        // Insert ferry schedule details
        $stmt = $conn->prepare("INSERT INTO ferry_schedule (ferry_id, departure_port, arrival_port, departure_time, arrival_time, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $ferry_id, $departure_port, $arrival_port, $departure_time, $arrival_time, $status);
        
        if ($stmt->execute()) {
            logAdminAction($_SESSION['admin_id'], 'add new ferry', $ferry_id);
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
        $stmt = $conn->prepare("SELECT accom_id FROM accommodation WHERE accom_type = ?");
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
                logAdminAction($_SESSION['admin_id'], 'add new accommodation price', $ferry_id);
            } else {
                $error_message = "Error adding accommodation price: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Fetch list of ferries and accommodation types for dropdowns
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
    /* Basic reset */
    body, h2, h3, p, form, input, select, button {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
    }
    body {
        background-color: #f4f4f4;
        color: #333;
        padding: 20px;
    }
    .container {
        width: 70%;
        margin: 0 auto;
        background-color: #fff;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    h2 {
        text-align: center;
        color: #007bff;
    }
    h3 {
        color: #007bff;
        margin-bottom: 10px;
    }
    .input-group {
        margin-bottom: 15px;
    }
    .input-group input, .input-group select {
        width: 100%;
        padding: 10px;
        margin: 5px 0;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }
    .input-group label {
        font-weight: bold;
    }
    .btn {
        width: 100%;
        padding: 10px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .btn:hover {
        background-color: #0056b3;
    }
    .success-message, .error-message {
        text-align: center;
        padding: 10px;
        margin-bottom: 20px;
        border-radius: 5px;
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
                <?php while ($row = $ferries->fetch_assoc()): ?>
                    <option value="<?php echo $row['ferry_name']; ?>"><?php echo $row['ferry_name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="input-group">
            <label for="accom_type">Accommodation Type</label>
            <select name="accom_type" required>
                <option value="">Select Accommodation Type</option>
                <?php while ($row = $accommodation_types->fetch_assoc()): ?>
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

</div>

</body>
</html>

<?php include 'footer.php'; ?>
