<?php
// Include your database connection
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');
include 'header.php';

// Set pagination variables
$limit = 10; // Set the number of rows per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

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
    ORDER BY fs.ferry_id, a.accom_type
    LIMIT $limit OFFSET $offset;
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
        <link href='https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css' rel='stylesheet'>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f8f9fa;
                margin: 0;
                padding: 0;
            }
            .header {
                text-align: center;
                margin: 20px 0;
            }
            .back-button a {
                text-decoration: none;
                font-size: 18px;
                color: white;
                background-color: #007BFF;
                padding: 12px 20px;
                border-radius: 8px;
                transition: background-color 0.3s;
            }
            .back-button a:hover {
                background-color: #0056b3;
            }
            table {
                width: 100%;
                margin-top: 20px;
                border-collapse: collapse;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 12px;
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
                padding: 8px 15px;
                border-radius: 5px;
                margin: 0 5px;
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
            .pagination a {
                padding: 8px 15px;
                margin: 0 8px;
                border: 1px solid #ddd;
                color: #007BFF;
                text-decoration: none;
                border-radius: 5px;
            }
            .pagination a:hover {
                background-color: #f2f2f2;
            }
            .no-schedules {
                text-align: center;
                padding: 40px;
                background-color: #f8f9fa;
                border-radius: 5px;
            }
            .no-schedules .back-button {
                font-size: 18px;
                color: white;
                background-color: #007BFF;
                padding: 12px 20px;
                border-radius: 8px;
                transition: background-color 0.3s;
            }
            .no-schedules .back-button:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>Ferry Schedules and Rates</h1>
        </div>
        <div class='back-button'>
            <a href='admin_dashboard.php'>Back</a>
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
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>";

    // Loop through the result and display each row
    while ($row = $result->fetch_assoc()) {
        $status_color = $row['status'] === 'active' ? 'green' : 'red';
        $status_icon = $row['status'] === 'active' ? "<i class='fas fa-check-circle' style='color: green;'></i>" : "<i class='fas fa-times-circle' style='color: red;'></i>";
        echo "<tr>
            <td>{$row['ferry_id']}</td>
            <td>{$row['departure_port']}</td>
            <td>{$row['arrival_port']}</td>
            <td>{$row['departure_time']}</td>
            <td>{$row['arrival_time']}</td>
            <td>{$row['accom_type']}</td>
            <td>" . number_format($row['price'], 2) . "</td>
            <td style='color: $status_color;'>$status_icon</td>
            <td class='action-buttons'>
                <a href='edit_schedule_and_rates.php?ferry_id={$row['ferry_id']}' class='edit-btn'>
                    <i class='fas fa-edit'></i> Edit
                </a>
                <a href='#' class='delete-btn' data-toggle='modal' data-target='#deleteModal' data-id='{$row['ferry_id']}'>
                    <i class='fas fa-trash'></i> Delete
                </a>
            </td>
        </tr>";
    }

    echo "</tbody>
        </table>";

    // Pagination
    $total_query = "SELECT COUNT(*) AS total FROM ferry_schedule WHERE status = 'active'";
    $total_result = $conn->query($total_query);
    $total_rows = $total_result->fetch_assoc()['total'];
    $total_pages = ceil($total_rows / $limit);

    echo "<div class='pagination'>";
    for ($i = 1; $i <= $total_pages; $i++) {
        echo "<a href='schedule_and_rates.php?page=$i'>$i</a> ";
    }
    echo "</div>";

} else {
    echo "<div class='no-schedules'>
            <p>No active ferry schedules found.</p>
            <a href='admin_dashboard.php' class='back-button'>Back</a>
        </div>";
}

// Close the database connection
$conn->close();
?>
<!-- Modal for confirmation -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Delete Ferry Schedule</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this ferry schedule? This action cannot be undone.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <a href="#" id="confirmDelete" class="btn btn-danger">Delete</a>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    // Capture the data-id attribute and set it for the confirmation link
    $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); 
        var ferryId = button.data('id'); 
        var modal = $(this);
        modal.find('#confirmDelete').attr('href', 'delete_schedule.php?ferry_id=' + ferryId);
    });
</script>
<?php include 'footer.php'; ?>
