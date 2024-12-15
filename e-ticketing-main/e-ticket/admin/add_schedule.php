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

if (isset($_POST['add_ferry_name'])) {
    $ferry_name = $_POST['ferry_name'];

    // Insert ferry name into the Ferries table
    $stmt = $conn->prepare("INSERT INTO ferries (ferry_name) VALUES (?)");
    $stmt->bind_param("s", $ferry_name);
    if ($stmt->execute()) {
        $success_message = "Ferry name added successfully.";
        logAddFerry($admin_id, $action, $target_id);
    } else {
        $error_message = "Error adding ferry: " . $stmt->error;
    }
    $stmt->close();
}
if (isset($_POST['add_ferry_schedule'])) {
    $ferry_id = $_POST['ferry_id']; // Ferry selected from a dropdown
    $departure_port = $_POST['departure_port'];
    $arrival_port = $_POST['arrival_port'];
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];
    $status = $_POST['status'];

    // Insert schedule into the Ferry_Schedule table
    $stmt = $conn->prepare("INSERT INTO ferry_schedule (ferry_id, departure_port, arrival_port, departure_time, arrival_time, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $ferry_id, $departure_port, $arrival_port, $departure_time, $arrival_time, $status);
    if ($stmt->execute()) {
        $success_message = "Ferry schedule added successfully.";
        header('Location: view_ferries.php');
        exit();
    } else {
        $error_message = "Error adding ferry schedule: " . $stmt->error;
    }
   $stmt->close();
}
$ferries = $conn->query("SELECT ferry_id, ferry_name FROM ferries ORDER BY ferry_name");
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
    background-color: #eef2f5;
    margin: 0;
    padding: 0;
    color: #333;
    line-height: 1.6;
}

/* Container Styling */
.container {
    width: 85%;
    max-width: 900px;
    margin: 20px auto;
    padding: 20px;
    background-color: #ffffff;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
    border-radius: 12px;
    overflow: hidden;
}

/* Header Styling */
h2 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 30px;
    font-size: 30px;
    letter-spacing: 1.2px;
    font-weight: bold;
}

/* Success and Error Messages */
.success-message, .error-message {
    padding: 15px;
    border-radius: 8px;
    text-align: center;
    margin-bottom: 20px;
    font-size: 16px;
}

.success-message {
    color: #155724;
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
}

.error-message {
    color: #721c24;
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
}

/* Input Groups */
.input-group {
    margin-bottom: 20px;
    position: relative;
}

.input-group input,
.input-group select,
.input-group textarea {
    width: 100%;
    padding: 12px;
    font-size: 16px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    background-color: #fff;
    transition: all 0.3s ease;
    box-sizing: border-box;
}

.input-group input:focus,
.input-group select:focus {
    border-color: #007bff;
    box-shadow: 0 0 8px rgba(0, 123, 255, 0.25);
    outline: none;
}

/* Button Styling */
.btn {
    background-color: #007bff; 
    color: white; 
    padding: 12px 20px; 
    font-size: 16px; 
    border: none; 
    border-radius: 8px; 
    cursor: pointer; 
    transition: all 0.3s ease;
    margin-bottom: 10px;
    width: calc(50% - 10px);
}

.btn:hover {
    background-color: #0056b3;
    transform: translateY(-3px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
}

/* Responsive Table */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 30px;
    border-radius: 8px;
    overflow: hidden;
    text-align: left;
}

table th, table td {
    padding: 15px;
    font-size: 16px;
    border-bottom: 1px solid #dee2e6;
}

table th {
    background-color: #f1f3f5;
    font-weight: bold;
}

table tr:nth-child(odd) {
    background-color: #f9fbfc;
}

table tr:hover {
    background-color: #f2f4f6;
    cursor: pointer;
}

/* Mobile Styles */
@media (max-width: 768px) {
    .container {
        width: 95%;
    }

    .btn {
        width: 100%;
    }

    table th, table td {
        font-size: 14px;
        padding: 10px;
    }
}
.modal-confirm {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: white;
    padding: 20px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    z-index: 1000;
}
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 999;
}
/* General Styles */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
}

/* Navigation Menu */
.menu {
    display: flex;
    gap: 15px;
    background-color: #333;
    padding: 10px;
}
.menu a, .menu .btn-logout {
    color: white;
    text-decoration: none;
    padding: 10px 15px;
    border-radius: 5px;
    background-color: #555;
    border: none;
    cursor: pointer;
    font-size: 14px;
}
.menu a:hover, .menu .btn-logout:hover {
    background-color: #777;
}

/* Modal Styles */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}
.modal.hidden {
    display: none;
}
.modal-content {
    background-color: white;
    padding: 20px;
    width: 300px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}
.modal-content h3 {
    margin: 0 0 10px;
}
.modal-content p {
    margin: 0 0 20px;
}
.modal-actions {
    display: flex;
    justify-content: space-between;
    gap: 10px;
}
.modal-actions .btn {
    padding: 10px 15px;
    font-size: 14px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
.modal-actions .btn-primary {
    background-color: #007bff;
    color: white;
}
.modal-actions .btn-primary:hover {
    background-color: #0056b3;
}
.modal-actions .btn-secondary {
    background-color: #6c757d;
    color: white;
}
.modal-actions .btn-secondary:hover {
    background-color: #5a6268;
}


</style>
<script>
// document.addEventListener("DOMContentLoaded", function() {
//     // Add event listener to reset form after submission
//     const forms = document.querySelectorAll("form");
//     forms.forEach((form) => {
//         form.addEventListener("submit", function() {
//             setTimeout(() => {
//                 form.reset();
//             }, 1000);
//         });
//     });

    // Add confirmation prompt for adding a ferry
    const addFerryButton = document.querySelector("button[name='add_ferry']");
    if (addFerryButton) {
        addFerryButton.addEventListener("click", function(e) {
            if (!confirm("Are you sure you want to add this ferry?")) {
                e.preventDefault();
            }
        });
    }

    // Add confirmation prompt for adding a ferry schedule
    const addScheduleButton = document.querySelector("button[name='add_ferry_schedule']");
    if (addScheduleButton) {
        addScheduleButton.addEventListener("click", function(e) {
            if (!confirm("Are you sure you want to add this ferry schedule?")) {
                e.preventDefault();
            }
        });
    }
// });
document.getElementById("logoutBtn").addEventListener("click", function () {
    if (confirm("Are you sure you want to logout?")) {
        window.location.href = "/logout"; // Redirect to logout URL
    }
});

</script>
</head>
<body>
    <!-- Navigation Menu -->
<div class="menu">
    <a href="manage_users.php">Manage Users</a>
    <a href="view_reservation.php">View Bookings</a>
    <a href="view_ferries.php">View Ferries</a>
    <a href="reports.php">Reports</a>
    <button onclick="openLogoutModal()" class="btn-logout">Logout</button>
</div>

<!-- Logout Confirmation Modal -->
<div id="logoutModal" class="modal hidden">
    <div class="modal-content">
        <h3>Confirm Logout</h3>
        <p>Are you sure you want to log out?</p>
        <div class="modal-actions">
            <button onclick="logout()" class="btn btn-primary">Yes, Logout</button>
            <button onclick="closeLogoutModal()" class="btn btn-secondary">Cancel</button>
        </div>
    </div>
</div>


    <div class="container">
        <h2>Manage Ferries</h2>

        <!-- Success/Error Messages -->
        <?php if (isset($success_message)): ?>
            <div class="success-message">
                <?= htmlspecialchars($success_message); ?>
                <button class="close-btn" onclick="dismissAlert(this)">&times;</button>
            </div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <?= htmlspecialchars($error_message); ?>
                <button class="close-btn" onclick="dismissAlert(this)">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Add Ferry Form -->
        <form id="addFerryForm" method="POST" action="">
            <h3>Add Ferry</h3>
            <div class="input-group">
                <label for="ferry_name">Ferry Name:</label>
                <input type="text" id="ferry_name" name="ferry_name" placeholder="Ferry Name" required>
            </div>
            <button type="submit" name="add_ferry" class="btn">Add Ferry</button>
        </form>

        <!-- Add Ferry Schedule Form -->
        <form id="addScheduleForm" method="POST" action="">
            <h3>Add Ferry Schedule</h3>
            <div class="input-group">
                <label for="ferry_id">Ferry:</label>
                <select id="ferry_id" name="ferry_id" required>
                    <?php while ($ferry = $ferries->fetch_assoc()): ?>
                        <option value="<?= $ferry['ferry_id'] ?>"><?= $ferry['ferry_name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="input-group">
                <label for="departure_port">Departure Port:</label>
                <input type="text" id="departure_port" name="departure_port" placeholder="Enter Departure Port" required>
            </div>
            <div class="input-group">
                <label for="arrival_port">Arrival Port:</label>
                <input type="text" id="arrival_port" name="arrival_port" placeholder="Enter Arrival Port" required>
            </div>
            <div class="input-group">
                <label for="departure_time">Departure Time:</label>
                <input type="time" id="departure_time" name="departure_time" required>
            </div>
            <div class="input-group">
                <label for="arrival_time">Arrival Time:</label>
                <input type="time" id="arrival_time" name="arrival_time" required>
            </div>
            <div class="input-group">
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="" disabled selected>Select Ferry Status</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" name="add_ferry_schedule" class="btn">Add Schedule</button>
        </form>
    </div>

    <script>
        // Function to show logout modal
       // Open Logout Modal
function openLogoutModal() {
    const modal = document.getElementById("logoutModal");
    modal.classList.remove("hidden");
}

// Close Logout Modal
function closeLogoutModal() {
    const modal = document.getElementById("logoutModal");
    modal.classList.add("hidden");
}


        // Function to log out
        function logout() {
            window.location.href = 'admin_login.php';
        }

        // Dismiss alert messages
        function dismissAlert(element) {
            element.parentElement.style.display = 'none';
        }

      
       
    </script>
</body>

</html>
