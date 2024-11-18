<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: customer_login.php");
    exit();
}

// Include database connection
include('C:/xampp/htdocs/e-ticket/config.php');

// Fetch ferry schedules with accommodation
$schedules_query = "
    SELECT 
        fs.ferry_id, 
        fs.departure_port, 
        fs.arrival_port, 
        fs.departure_time, 
        fs.arrival_time,
        ap.price,
        a.accom_type
    FROM ferry_schedule fs
    LEFT JOIN accommodation_prices ap ON fs.ferry_id = ap.ferry_id
    LEFT JOIN accommodation a ON ap.accom_id = a.accom_price_id
    WHERE fs.status = 'active'
";
$schedules = $conn->query($schedules_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Trip</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        form {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        select, input[type="text"], input[type="number"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .total-display {
            font-weight: bold;
            font-size: 1.2em;
            text-align: center;
            margin-top: 20px;
        }
    </style>
    <script>
    function updateTotal() {
        const selectedOption = document.getElementById('ferry_id').selectedOptions[0];
        const price = parseFloat(selectedOption?.dataset.price || 0);
        const discountType = document.getElementById('discount_type').value;
        const numTickets = parseInt(document.getElementById('num_tickets').value || 0);

        // Calculate subtotal
        let subtotal = price * numTickets;
        let discount = 0;

        // Determine discount
        if (subtotal > 0) {
            switch (discountType) {
                case 'student':
                    discount = subtotal * 0.20; // 20% discount
                    break;
                case 'senior':
                    discount = subtotal * 0.30; // 30% discount
                    break;
                case 'pwd':
                    discount = subtotal * 0.20; // 20% discount
                    break;
                default:
                    discount = 0; // No discount
                    break;
            }
        }

        // Calculate total cost
        const totalCost = subtotal - discount;

        // Update the total display
        document.getElementById('total_display').innerText = 
            `Total Cost: ₱${totalCost.toFixed(2)}`;
    }

    // Automatically update total cost on page load and input changes
    document.addEventListener("DOMContentLoaded", () => {
        document.getElementById('ferry_id').addEventListener('change', updateTotal);
        document.getElementById('discount_type').addEventListener('change', updateTotal);
        document.getElementById('num_tickets').addEventListener('input', updateTotal);
    });
</script>

</head>
<body>
    <h1>Book a Trip</h1>
    <form method="POST" action="confirm_booking.php">
        <label for="ferry_id">Select Ferry Schedule:</label>
        <select name="ferry_id" id="ferry_id" onchange="updateTotal()" required>
            <option value="" data-price="0">-- Select Schedule --</option>
            <?php while ($row = $schedules->fetch_assoc()): ?>
                <option value="<?= $row['ferry_id'] ?>" data-price="<?= $row['price'] ?>">
                    Ferry ID <?= $row['ferry_id'] ?> 
                    (<?= $row['departure_port'] ?> → <?= $row['arrival_port'] ?> @ <?= $row['departure_time'] ?>) 
                    [<?= $row['accom_type'] ?>] - ₱<?= number_format($row['price'], 2) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="discount_type">Select Discount Type:</label>
        <select name="discount_type" id="discount_type" onchange="updateTotal()" required>
            <option value="regular">Regular</option>
            <option value="student">Student</option>
            <option value="senior">Senior Citizen</option>
            <option value="pwd">PWD</option>
        </select>

        <label for="passenger_name">Passenger Name:</label>
        <input type="text" name="passenger_name" id="passenger_name" required>

        <label for="contact">Contact Number:</label>
        <input type="text" name="contact" id="contact" required>

        <label for="num_tickets">Number of Tickets:</label>
        <input type="number" name="num_tickets" id="num_tickets" min="1" onchange="updateTotal()" required>

        <div id="total_display" class="total-display">Total Cost: ₱0.00</div>

        <input type="submit" value="Book Now">
    </form>
</body>
</html>
