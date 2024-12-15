<?php
session_start();
include ('C:/xampp/htdocs/e-ticket/config.php'); // Ensure the correct path to your config.php

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if a schedule_id is passed for deletion
if (isset($_GET['schedule_id'])) {
    // Sanitize the schedule_id to prevent SQL injection
    $schedule_id = intval($_GET['schedule_id']);

    // Delete query
    $delete_sql = "DELETE FROM ferry_schedule WHERE schedule_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $schedule_id);

    if ($delete_stmt->execute()) {
        echo "Schedule deleted successfully!";
        header("Location: view_ferries.php"); // Redirect to the ferry schedule view page
        exit();
    } else {
        echo "Error deleting schedule: " . $conn->error;
    }
}
// Get search term and sanitize
$search_term = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Query to fetch ferry schedule with optional search
$sql = "SELECT fs.ferry_id, fs.schedule_id, f.ferry_name, fs.departure_port, fs.arrival_port, fs.departure_time, fs.arrival_time, fs.status FROM ferry_schedule fs INNER JOIN ferries f ON fs.ferry_id = f.ferry_id";

if (!empty($search_term)) {
    $sql .= " WHERE f.ferry_name LIKE '%$search_term%' OR fs.departure_port LIKE '%$search_term%' OR fs.arrival_port LIKE '%$search_term%'";
}

$sql .= " ORDER BY departure_time ASC";

// Query to fetch ferry schedule
$sql = "SELECT fs.ferry_id, fs.schedule_id, f.ferry_name,
fs.departure_port,
  fs.arrival_port, 
  fs.departure_time,
  fs.arrival_time,
 fs.status from ferry_schedule fs INNER JOIN ferries f  on 
fs.ferry_id = f.ferry_id ORDER BY departure_time ASC";
$result = $conn->query($sql);

// Start the HTML output
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ferry Schedule</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #007bff;
        }

        .top-controls {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .top-controls .search-bar input {
            padding: 10px;
            font-size: 16px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .top-controls .add-schedule-btn {
            padding: 10px 20px;
            background: #28a745;
            color: #fff;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .top-controls .add-schedule-btn:hover {
            background: #218838;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .status {
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 4px;
        }

        .active {
            color: white;
            background-color: #28a745; /* Green */
        }

        .inactive {
            color: white;
            background-color: #dc3545; /* Red */
        }

        .action-btns {
            display: flex;
            justify-content: space-around;
            margin-top: 10px;
        }

        .action-btns a {
            padding: 8px 15px;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
        }

        .update-btn {
            background-color: #007bff; /* Blue */
        }

        .update-btn:hover {
            background-color: #0056b3;
        }

        .delete-btn {
            background-color: #dc3545; /* Red */
        }

        .delete-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>';

echo '<div class="container">';
echo '<h2>Ferry Schedule</h2>';

// Top controls (search bar and add schedule button)
echo '<div class="top-controls">
        <div class="search-bar">
            <form action="view_ferries.php" method="GET">
                <input type="text" name="search" placeholder="Search by ferry name or port..." value="' . (isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '') . '" />
            </form>
        </div>
        <a href="add_schedule.php" class="add-schedule-btn">Add Ferry Schedule</a>
      </div>';

// Check if there are results
if ($result->num_rows > 0) {
    // Start the HTML table
    echo "<table>";
    echo "<thead><tr>
            <th>Ferry Name</th>
            <th>Departure Time</th>
            <th>Arrival Time</th>
            <th>Departure Port</th>
            <th>Arrival Port</th>
            <th>Status</th>
            <th>Actions</th>
          </tr></thead><tbody>";

    // Output data of each row
    while($row = $result->fetch_assoc()) {
        // Determine status class based on ferry status
        $status_class = '';
        if ($row["status"] == "active") {
            $status_class = 'active';
        } elseif ($row["status"] == "inactive") {
            $status_class = 'inactive';
        }

        echo "<tr>
                <td>" . htmlspecialchars($row["ferry_name"]) . "</td>
                <td>" . htmlspecialchars($row["departure_time"]) . "</td>
                <td>" . htmlspecialchars($row["arrival_time"]) . "</td>
                <td>" . htmlspecialchars($row["departure_port"]) . "</td>
                <td>" . htmlspecialchars($row["arrival_port"]) . "</td>
                <td><span class='status $status_class'>" . htmlspecialchars($row["status"]) . "</span></td>
                <td class='action-btns'>
                    <a href='update_schedule.php?schedule_id=" . $row["schedule_id"] . "' class='update-btn'>Update</a>
                    <a href='view_ferries.php?schedule_id=" . $row["schedule_id"] . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this schedule?\")'>Delete</a>
                </td>
              </tr>";
    }

    echo "</tbody></table>";
} else {
    echo "<p>No ferry schedules available.</p>";
}

echo '</div>';  // End container div

// Close the connection
$conn->close();

echo '</body></html>';
?>
