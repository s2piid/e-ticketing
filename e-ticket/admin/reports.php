<?php
session_start();
include('C:/xampp/htdocs/e-ticket/config.php'); // Include the config to connect to the database

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Function to log admin actions
function logAdminAction($admin_id, $action, $target_id) {
    global $conn;
    
    // Prepare and execute the log insert query
    $stmt = $conn->prepare("INSERT INTO Admin_Actions_Log (admin_id, action, target_id) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $admin_id, $action, $target_id);
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

// Example usage: Logging when an admin adds a new ferry
if (isset($_POST['add_ferry'])) {
    $ferry_name = $_POST['ferry_name'];
    $departure_port = $_POST['departure_port'];
    $arrival_port = $_POST['arrival_port'];
    $status = $_POST['status'];
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];

    // Insert ferry into the database
    $stmt = $conn->prepare("INSERT INTO ferries (ferry_name, departure_port, arrival_port, departure_time, arrival_time, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $ferry_name, $departure_port, $arrival_port, $departure_time, $arrival_time, $status);
    if ($stmt->execute()) {
        // Log the admin action
        $ferry_id = $conn->insert_id;
        logAdminAction($_SESSION['admin_id'], 'add new ferry', $ferry_id);
        $success_message = "Ferry added successfully.";
    } else {
        $error_message = "Error adding ferry: " . $stmt->error;
    }
    $stmt->close();
}

// Example: Fetching logs for display
$logs = $conn->query("SELECT * FROM Admin_Actions_Log ORDER BY timestamp DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Activity Logs</title>
    <link rel="stylesheet" href="style.css">
    <style>/* style.css */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

.container {
    width: 80%;
    margin: 20px auto;
    background-color: white;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    margin-bottom: 20px;
}

.success-message {
    color: green;
    text-align: center;
    margin-bottom: 10px;
}

.error-message {
    color: red;
    text-align: center;
    margin-bottom: 10px;
}

.log-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.log-table th, .log-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

.log-table th {
    background-color: #f2f2f2;
}

.log-table tr:nth-child(even) {
    background-color: #f9f9f9;
}
</style>
</head>
<body>
    <div class="container">
        <h2>Admin Activity Logs</h2>
        
        <!-- Display success or error message -->
        <?php if (isset($success_message)): ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <input type="text" id="logSearch" onkeyup="searchLogs()" placeholder="Search for actions..">
        <table class="log-table">
            <thead>
                <tr>
                    <th>Admin ID</th>
                    <th>Action</th>
                    <th>Target ID</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $logs->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['admin_id']; ?></td>
                        <td><?php echo $row['action']; ?></td>
                        <td><?php echo $row['target_id']; ?></td>
                        <td><?php echo $row['timestamp']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<script>
    function searchLogs() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("logSearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("logTable");
        tr = table.getElementsByTagName("tr");

        for (i = 1; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[1]; // Search in the "Action" column
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
</script>
