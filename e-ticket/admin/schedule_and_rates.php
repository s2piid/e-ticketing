<?php
// Include your database connection
include('C:/xampp/htdocs/e-ticket/config.php');

// Fetch ferry schedules and join with accommodations and prices
$query = "
    SELECT 
        fs.ferry_id,
        fs.departure_port,
        fs.arrival_port,
        fs.departure_time,
        fs.arrival_time,
        fs.status,
        a.accom_type,
        ap.price
    FROM ferry_schedule fs
    LEFT JOIN accommodation_prices ap ON fs.ferry_id = ap.ferry_id
    LEFT JOIN accommodation a ON ap.accom_id = a.accom_price_id
    WHERE fs.status = 'active'
    ORDER BY fs.ferry_id, a.accom_type;
";

$result = $conn->query($query);

// Check if the query returns any rows
if ($result->num_rows > 0) {
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Ferry Schedules and Rates</title>
        <style>
            body {
                 font-family: Arial, sans-serif;
                background-color: #f8f9fa;
                margin-top: 0; /* Ensure no margin at the top */
                padding: 0;
            }
            .header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
            }
            .back-button a {
                text-decoration: none;
                font-size: 16px;
                color: white;
                background-color: #007BFF;
                padding: 10px 15px;
                border-radius: 5px;
                transition: background-color 0.3s;
            }
            .back-button a:hover {
                background-color: #0056b3;
            }
            table {
               width: 100%;
                margin-top: 0; /* Adjust the top margin of the table */
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: center;
            }
            th {
                background-color: #007BFF;
                color: white;
            }
            tr:nth-child(even) {
                background-color: #f2f2f2;
            }
            tr:hover {
                background-color: #ddd;
            }
            .action-buttons a {
                text-decoration: none;
                color: white;
                padding: 5px 10px;
                border-radius: 5px;
                margin: 0 2px;
            }
            .edit-btn {
                background-color: #28a745;
            }
            .edit-btn:hover {
                background-color: #218838;
            }
            .delete-btn {
                background-color: #dc3545;
            }
            .delete-btn:hover {
                background-color: #c82333;
            }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>Ferry Schedules and Rates</h1>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Ferry ID</th>
                    <th>Departure Port</th>
                    <th>Arrival Port</th>
                    <th>Departure Time</th>
                    <th>Arrival Time</th>
                    <th>Accommodation Type</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>";

    // Loop through the result and display each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['ferry_id']}</td>
            <td>{$row['departure_port']}</td>
            <td>{$row['arrival_port']}</td>
            <td>{$row['departure_time']}</td>
            <td>{$row['arrival_time']}</td>
            <td>{$row['accom_type']}</td>
            <td>" . number_format($row['price'], 2) . "</td>
            <td class='action-buttons'>
                <a href='edit_schedule.php?ferry_id={$row['ferry_id']}' class='edit-btn'>Edit</a>
                <a href='delete_schedule.php?ferry_id={$row['ferry_id']}' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this schedule?\");'>Delete</a>
            </td>
        </tr>";
    }

    echo "</tbody>
        </table>
    </body>
    </html>";
} else {
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Ferry Schedules and Rates</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                padding: 20px;
            }
            .header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
            }
            .back-button a {
                text-decoration: none;
                font-size: 16px;
                color: white;
                background-color: #007BFF;
                padding: 10px 15px;
                border-radius: 5px;
                transition: background-color 0.3s;
            }
            .back-button a:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>Ferry Schedules and Rates</h1>
            <div class='back-button'>
                <a href='customer_dashboard.php'>Back to Dashboard</a>
            </div>
        </div>
        <p>No active ferry schedules found.</p>
    </body>
    </html>";
}

// Close the database connection
$conn->close();
?>