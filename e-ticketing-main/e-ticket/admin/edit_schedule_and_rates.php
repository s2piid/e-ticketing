<?php
// Include your database connection
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');
include 'header.php';

// Check if the ferry_id is passed in the URL
if (isset($_GET['ferry_id'])) {
    $ferry_id = $_GET['ferry_id'];

    // Fetch the ferry schedule details based on the ferry_id
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
        WHERE fs.ferry_id = $ferry_id;
    ";
    
    $result = $conn->query($query);

    // Check if the ferry schedule exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        // If ferry schedule not found, redirect to the schedule list page
        header('Location: schedule_and_rates.php');
        exit;
    }
} else {
    // If no ferry_id is passed, redirect to the schedule list page
    header('Location: schedule_and_rates.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the updated values from the form
    $departure_port = $_POST['departure_port'];
    $arrival_port = $_POST['arrival_port'];
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];
    $status = $_POST['status'];
    $accom_type = $_POST['accom_type'];
    $price = $_POST['price'];

    // Update the ferry schedule in the database
    $update_query = "
        UPDATE ferry_schedule fs
        JOIN accommodation_prices ap ON fs.ferry_id = ap.ferry_id
        SET 
            fs.departure_port = '$departure_port',
            fs.arrival_port = '$arrival_port',
            fs.departure_time = '$departure_time',
            fs.arrival_time = '$arrival_time',
            fs.status = '$status',
            ap.accom_type = '$accom_type',
            ap.price = '$price'
        WHERE fs.ferry_id = $ferry_id;
    ";

    if ($conn->query($update_query) === TRUE) {
        // Redirect to the schedule list page if update is successful
        header('Location: schedule_and_rates.php');
        exit;
    } else {
        $error_message = "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Ferry Schedule</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin: 30px 0;
        }
        .back-button a {
            text-decoration: none;
            font-size: 16px;
            color: white;
            background-color: #007BFF;
            padding: 12px 20px;
            border-radius: 8px;
            transition: background-color 0.3s;
            display: inline-block;
            margin-top: 15px;
        }
        .back-button a:hover {
            background-color: #0056b3;
        }
        .form-container {
            max-width: 650px;
            margin: 40px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .form-group label {
            font-size: 15px;
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007BFF;
            border-color: #007BFF;
            padding: 12px 20px;
            font-size: 16px;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .alert-danger {
            margin-top: 15px;
            padding: 10px;
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Edit Ferry Schedule</h1>
</div>

<div class="back-button">
    <a href="schedule_and_rates.php">Back</a>
</div>

<?php if (isset($error_message)): ?>
    <div class="alert alert-danger">
        <?php echo $error_message; ?>
    </div>
<?php endif; ?>

<div class="form-container">
    <form id="scheduleForm" method="POST" action="edit_schedule_and_rates.php?ferry_id=<?php echo $ferry_id; ?>">
        <div class="form-group">
            <label for="departure_port">Departure Port</label>
            <input type="text" class="form-control" id="departure_port" name="departure_port" value="<?php echo $row['departure_port']; ?>" required>
        </div>
        <div class="form-group">
            <label for="arrival_port">Arrival Port</label>
            <input type="text" class="form-control" id="arrival_port" name="arrival_port" value="<?php echo $row['arrival_port']; ?>" required>
        </div>
        <div class="form-group">
            <label for="departure_time">Departure Time</label>
            <input type="time" class="form-control" id="departure_time" name="departure_time" value="<?php echo $row['departure_time']; ?>" required>
        </div>
        <div class="form-group">
            <label for="arrival_time">Arrival Time</label>
            <input type="time" class="form-control" id="arrival_time" name="arrival_time" value="<?php echo $row['arrival_time']; ?>" required>
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select class="form-control" id="status" name="status" required>
                <option value="active" <?php echo $row['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo $row['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>
        <div class="form-group">
            <label for="accom_type">Accommodation Type</label>
            <input type="text" class="form-control" id="accom_type" name="accom_type" value="<?php echo $row['accom_type']; ?>" required>
        </div>
        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" class="form-control" id="price" name="price" value="<?php echo number_format($row['price'], 2, '.', ''); ?>" step="0.01" required>
        </div>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#confirmModal">Update Schedule</button>
    </form>
</div>

<!-- Modal for confirmation -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirm Update</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to update this ferry schedule?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

<?php include 'footer.php'; ?>
