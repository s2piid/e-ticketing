<?php
session_start();
include('C:/xampp/htdocs/e-ticket/config.php'); // Ensure the correct path

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handling Confirm and Cancel actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'];
    $action = $_POST['action'];

    if ($action === 'confirm') {
        // Update booking status to confirmed
        $query = "UPDATE bookings SET status = 'Confirmed' WHERE booking_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $booking_id);
        if ($stmt->execute()) {
            $success_message = "Booking ID $booking_id has been confirmed.";
        } else {
            $error_message = "Failed to confirm booking ID $booking_id.";
        }
    } elseif ($action === 'cancel') {
        // Update booking status to cancelled
        $query = "UPDATE bookings SET status = 'Cancelled' WHERE booking_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $booking_id);
        if ($stmt->execute()) {
            $success_message = "Booking ID $booking_id has been cancelled.";
        } else {
            $error_message = "Failed to cancel booking ID $booking_id.";
        }
    }
}

// Fetch all bookings along with ferry schedule and cost details
$query = "
    SELECT 
        b.booking_id, 
        u.username, 
        f.ferry_name, 
        b.booking_date, 
        b.status, 
        b.sub_price, 
        b.discount, 
        b.total_cost, 
        fs.departure_time, 
        fs.arrival_time 
    FROM 
        bookings b
    INNER JOIN 
        ferry_schedule fs 
    ON 
        b.fk_ferry_id = fs.ferry_id
    INNER JOIN 
        users u
    ON 
        b.fk_user_id = u.user_id 
    INNER JOIN
        ferries f 
    ON 
        b.fk_ferry_id = f.ferry_id
    ORDER BY 
        b.booking_ID ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Bookings</title>
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
.container {
    width: 90%;
    max-width: 2000px;
    margin: 20px auto;
    padding: 20px;
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    border-radius: 12px;
}
h2 {
    text-align: center;
    color: #4a4a4a;
    margin-bottom: 30px;
    font-size: 28px;
}
.success-message, .error-message {
    padding: 12px;
    border-radius: 8px;
    text-align: center;
    margin-bottom: 20px;
}
.success-message {
    color: #155724;
    background-color: #d4edda;
}
.error-message {
    color: #721c24;
    background-color: #f8d7da;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 30px;
}
table th, table td {
    padding: 15px;
    border: 1px solid #dee2e6;
}
table th {
    background-color: #e9ecef;
}
.btn-action {
    padding: 10px 15px;
    font-size: 14px;
    border-radius: 6px;
    cursor: pointer;
    border: none;
    transition: background-color 0.3s;
}
.btn-confirm {
    background-color: #28a745;
    color: #fff;
}
.btn-cancel {
    background-color: #dc3545;
    color: #fff;
}
.btn-confirm:hover {
    background-color: #218838;
}
.btn-cancel:hover {
    background-color: #c82333;
}
.menu {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
    gap: 15px;
}
.menu a, .btn-logout {
    padding: 12px 20px;
    background: #17a2b8;
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
}
.btn-logout {
    background: #dc3545;
}
.btn-logout:hover {
    background: #c82333;
}
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
        <h2>Customer Bookings</h2>

        <?php if (isset($success_message)): ?>
            <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Reference ID</th>
                    <th>Booking Date</th>
                    <th>Username</th>
                    <th>Ferry Name</th>
                    <th>Departure</th>
                    <th>Arrival</th>
                    <th>Subtotal</th>
                    <th>Discount Type</th>
                    <th>Total Cost</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['booking_id']; ?></td>
                        <td><?php echo $row['booking_date']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['ferry_name']; ?></td>
                        <td><?php echo $row['departure_time']; ?></td>
                        <td><?php echo $row['arrival_time']; ?></td>
                        <td><?php echo $row['sub_price']; ?></td>
                        <td><?php echo $row['discount_type']; ?></td>
                        <td><?php echo $row['total_cost']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td>
                            <form method="post" style="display:inline-block;">
                                <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                                <button type="submit" name="action" value="confirm" class="btn-action btn-confirm">Confirm</button>
                                <button type="submit" name="action" value="cancel" class="btn-action btn-cancel">Cancel</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
