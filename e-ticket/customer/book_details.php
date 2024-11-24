<?php
session_start();
include('C:/xampp/htdocs/e-ticket/config.php'); // Ensure the correct path

// Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

// Fetch data from the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $departure = $_POST['departure'] ?? '';
    $destination = $_POST['destination'] ?? '';
    $departure_date = $_POST['departure_date'] ?? '';
    $passengers = intval($_POST['passengers'] ?? 0);
} else {
    die("No form data submitted.");
}

// Ensure required fields are present
if (empty($departure) || empty($destination) || empty($departure_date) || $passengers < 1) {
    die("Invalid form data submitted.");
}

// Fetch ferry details
$sql1 = "SELECT f.ferry_id, f.ferry_name 
         FROM ferries f 
         INNER JOIN ferry_schedule fs ON f.ferry_id = fs.ferry_id
         WHERE fs.departure_port = ? AND fs.arrival_port = ?";
$stmt2 = $conn->prepare($sql1);
if (!$stmt2) {
    die("SQL Error: " . $conn->error);
}
$stmt2->bind_param("ss", $departure, $destination);
$stmt2->execute();
$result = $stmt2->get_result();
$stmt2->close();

// Fetch accommodation types with prices for the ferries
$accom_query = "
    SELECT a.accom_price_id, a.accom_type, fs.ferry_id, ap.price
    FROM accommodation_prices ap
    JOIN accommodation a ON ap.accom_id = a.accom_price_id
    JOIN ferry_schedule fs ON ap.ferry_id = fs.ferry_id
    WHERE fs.departure_port = ? AND fs.arrival_port = ?";
$accom_stmt = $conn->prepare($accom_query);
if (!$accom_stmt) {
    die("SQL Error: " . $conn->error);
}
$accom_stmt->bind_param("ss", $departure, $destination);
$accom_stmt->execute();
$accom_result = $accom_stmt->get_result();
$accom_stmt->close();

// Group accommodations by ferry_id
$accommodations = [];
while ($accom_row = $accom_result->fetch_assoc()) {
    $accommodations[$accom_row['ferry_id']][] = $accom_row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: none; }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="card p-4">
        <h2 class="text-center mb-4">Passenger Details</h2>
        <div class="mb-4">
            <p><strong>Departure:</strong> <?= htmlspecialchars($departure) ?></p>
            <p><strong>Destination:</strong> <?= htmlspecialchars($destination) ?></p>
            <p><strong>Departure Date:</strong> <?= htmlspecialchars($departure_date) ?></p>
        </div>

        <form action="process_booking.php" method="POST" enctype="multipart/form-data">
            <!-- Ferry Selection -->
            <div class="mb-4">
                <label for="ferry_id" class="form-label">Select Ferry:</label>
                <select name="ferry_id" id="ferry_id" class="form-select" required>
                    <option value="" disabled selected>Select Ferry</option>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row['ferry_id']) . "'>" . htmlspecialchars($row['ferry_name']) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Accommodation Selection -->
            <div class="mb-4">
                <label for="accom_id" class="form-label">Select Accommodation:</label>
                <select name="accom_id" id="accom_id" class="form-select" required>
                    <option value="" disabled selected>Select Accommodation</option>
                    <?php
                    foreach ($accommodations as $ferry_id => $accom_list) {
                        echo "<optgroup label='Ferry " . htmlspecialchars($ferry_id) . "'>";
                        foreach ($accom_list as $accom) {
                            echo "<option value='" . htmlspecialchars($accom['accom_price_id']) . "' data-price='" . htmlspecialchars($accom['price']) . "'>";
                            echo htmlspecialchars($accom['accom_type']) . " - $" . number_format($accom['price'], 2);
                            echo "</option>";
                        }
                        echo "</optgroup>";
                    }
                    ?>
                </select>
            </div>

            <!-- Passenger Details -->
            <?php for ($i = 1; $i <= $passengers; $i++): ?>
                <div class="card mb-4 p-3">
                    <h5>Passenger <?= $i ?></h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name[]" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Middle Name</label>
                            <input type="text" name="middle_name[]" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name[]" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gender</label>
                            <select name="gender[]" class="form-select" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Birth Date</label>
                            <input type="date" name="birth_date[]" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Civil Status</label>
                            <select name="civil_status[]" class="form-select">
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Widowed">Widowed</option>
                                <option value="Divorced">Divorced</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nationality</label>
                            <input type="text" name="nationality[]" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Address</label>
                            <input type="text" name="address[]" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Passenger Type</label>
                            <select name="passenger_type[]" class="form-select">
                                <option value="Regular">Regular</option>
                                <option value="Student">Student</option>
                                <option value="Child">Child</option>
                                <option value="PWD">PWD</option>
                                <option value="Infant">Infant</option>
                                <option value="Senior Citizen">Senior Citizen</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Upload Valid ID</label>
                            <input type="file" name="id_photo[]" class="form-control" accept="image/*" required>
                        </div>
                    </div>
                </div>
            <?php endfor; ?>

            <input type="hidden" name="departure" value="<?= htmlspecialchars($departure) ?>">
            <input type="hidden" name="destination" value="<?= htmlspecialchars($destination) ?>">
            <input type="hidden" name="departure_date" value="<?= htmlspecialchars($departure_date) ?>">
            <input type="hidden" name="passengers" value="<?= $passengers ?>">

            <div id="payment_details" class="mb-4">
                <p><strong>Accommodation Price:</strong> $0.00</p>
                <p><strong>Total Fare for All Passengers:</strong> $0.00</p>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const accomSelect = document.getElementById('accom_id');
        const passengers = <?= $passengers ?>;
        const paymentDetails = document.getElementById('payment_details');

        accomSelect.addEventListener('change', () => {
            const selectedOption = accomSelect.options[accomSelect.selectedIndex];
            const price = parseFloat(selectedOption.dataset.price || 0);

            const totalFare = price * passengers;

            paymentDetails.innerHTML = `
                <p><strong>Accommodation Price:</strong> $${price.toFixed(2)}</p>
                <p><strong>Total Fare for All Passengers:</strong> $${totalFare.toFixed(2)}</p>
            `;
        });
    });
</script>
</body>
</html>
