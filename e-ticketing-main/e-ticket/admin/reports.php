<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');

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
</style>
</head>
<body>
<br><br>


<script>
    function logout() {
        if (confirm("Are you sure you want to log out?")) {
            window.location.href = 'admin_login.php';
        }
    }
</script>
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
