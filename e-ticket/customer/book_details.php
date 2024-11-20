<?php
session_start();
include('C:/xampp/htdocs/e-ticket/config.php'); // Ensure the correct path

// Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

// Fetch user details from the database
$user_id = $_SESSION['customer_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the form
    $departure = $_POST['departure'] ?? '';
    $destination = $_POST['destination'] ?? '';
    $departure_date = $_POST['departure_date'] ?? '';
    $passengers = intval($_POST['passengers'] ?? 0); // Ensure it's an integer

} else {
    die("No form data submitted.");
}

// Ensure required fields are present
if (empty($departure) || empty($destination) || empty($departure_date) || $passengers < 1) {
    die("Invalid form data submitted.");
}

// Fetch ferry details
$sql1 = "SELECT f.ferry_name 
         FROM ferries f 
         INNER JOIN ferry_schedule fs ON f.ferry_id = fs.ferry_id
         WHERE departure_port = ? AND arrival_port = ?";
$stmt2 = $conn->prepare($sql1);
if (!$stmt2) {
    die("SQL Error: " . $conn->error);
}
$stmt2->bind_param("ss", $departure, $destination);
$stmt2->execute();
$result = $stmt2->get_result(); // Retrieve the result here
$stmt2->close(); // Close the statement after fetching the result

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Details</title>
    <!-- Include Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
        }
        .form-label {
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
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
                    <label for="ferry_name" class="form-label">Select Ferry:</label>
                    <select name="ferry_name" id="ferry_name" class="form-select" required>
                        <option value="" disabled selected>Select Ferry</option>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($row['ferry_name']) . "'>" . htmlspecialchars($row['ferry_name']) . "</option>";
                            }
                        } else {
                            echo "<option value='' disabled>No Ferries Available</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Passenger Details -->
                <?php for ($i = 1; $i <= $passengers; $i++): ?>
                <div class="card mb-4 p-3">
                    <h5>Passenger <?= $i ?></h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="first_name_<?= $i ?>" class="form-label">First Name</label>
                            <input type="text" name="first_name[]" id="first_name_<?= $i ?>" class="form-control" placeholder="Enter first name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="middle_name_<?= $i ?>" class="form-label">Middle Name</label>
                            <input type="text" name="middle_name[]" id="middle_name_<?= $i ?>" class="form-control" placeholder="Enter middle name">
                        </div>
                        <div class="col-md-6">
                            <label for="last_name_<?= $i ?>" class="form-label">Last Name</label>
                            <input type="text" name="last_name[]" id="last_name_<?= $i ?>" class="form-control" placeholder="Enter last name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="address_<?= $i ?>" class="form-label">Address</label>
                            <input type="text" name="address[]" id="address_<?= $i ?>" class="form-control" placeholder="Enter address" required>
                        </div>
                        <div class="col-md-6">
                            <label for="birthdate_<?= $i ?>" class="form-label">Birthdate</label>
                            <input type="date" name="birthdate[]" id="birthdate_<?= $i ?>" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="nationality_<?= $i ?>" class="form-label">Nationality</label>
                            <input type="text" name="nationality[]" id="nationality_<?= $i ?>" class="form-control" placeholder="Enter nationality" required>
                        </div>
                        <div class="col-md-6">
                            <label for="passenger_type_<?= $i ?>" class="form-label">Passenger Type</label>
                            <select name="passenger_type[]" id="passenger_type_<?= $i ?>" class="form-select" required>
                                <option value="" disabled selected>Select Type</option>
                                <option value="Adult">Adult</option>
                                <option value="Child">Child</option>
                                <option value="Senior">Senior</option>
                                <option value="PWD">PWD</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="valid_id_<?= $i ?>" class="form-label">Upload Valid ID</label>
                            <input type="file" name="valid_id[]" id="valid_id_<?= $i ?>" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>

                <!-- Hidden Inputs for Booking Data -->
                <input type="hidden" name="departure" value="<?= htmlspecialchars($departure) ?>">
                <input type="hidden" name="destination" value="<?= htmlspecialchars($destination) ?>">
                <input type="hidden" name="passengers" value="<?= $passengers ?>">

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
