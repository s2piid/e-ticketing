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
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
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
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>Ferry Schedules and Rates</h1>
            <div class='back-button'>
                <a href='customer_dashboard.php'>Back</a>
            </div>
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
