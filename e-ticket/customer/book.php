
<?php
session_start();
include('C:/xampp/htdocs/e-ticket/config.php'); // Ensure the correct path

// Check if the admin is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

// Fetch user details from the database
$user_id = $_SESSION['customer_id'];

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Fetch unique departure ports
$departureQuery = "SELECT DISTINCT departure_port FROM ferry_schedule WHERE status = 'active'";
$departureResult = $conn->query($departureQuery);

// Fetch unique arrival ports
$arrivalQuery = "SELECT DISTINCT arrival_port FROM ferry_schedule WHERE status = 'active'";
$arrivalResult = $conn->query($arrivalQuery);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking System</title>
    <!-- Include Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Book Your Ferry</h2>
        
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" id="bookingTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="destination-tab" data-bs-toggle="tab" data-bs-target="#destination" type="button" role="tab" aria-controls="destination" aria-selected="true">
                    Destination & Departure
                </button>
            </li>
            <!-- You can add more tabs here -->
        </ul>

        <!-- Tab Content -->
        <div class="tab-content mt-3" id="bookingTabsContent">
            <!-- Destination & Departure Tab -->
            <div class="tab-pane fade show active" id="destination" role="tabpanel" aria-labelledby="destination-tab">
                <form action="book_details.php" method="POST">
                    <div class="mb-3">
                        <label for="departure" class="form-label">Departure</label>
                        <select name="departure" id="departure" class="form-select" required>
                            <option value="" disabled selected>Select Departure</option>
                            <?php
                            // Populate departure options
                            if ($departureResult->num_rows > 0) {
                                while ($row = $departureResult->fetch_assoc()) {
                                    echo "<option value='" . $row['departure_port'] . "'>" . $row['departure_port'] . "</option>";
                                }
                            } else {
                                echo "<option value='' disabled>No Departures Available</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="destination" class="form-label">Destination</label>
                        <select name="destination" id="destination" class="form-select" required>
                            <option value="" disabled selected>Select Destination</option>
                            <?php
                            // Populate arrival options
                            if ($arrivalResult->num_rows > 0) {
                                while ($row = $arrivalResult->fetch_assoc()) {
                                    echo "<option value='" . $row['arrival_port'] . "'>" . $row['arrival_port'] . "</option>";
                                }
                            } else {
                                echo "<option value='' disabled>No Destinations Available</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Date of Departure -->
                    <div class="mb-3">
                        <label for="departure_date" class="form-label">Date of Departure</label>
                        <input type="date" name="departure_date" id="departure_date" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="passengers" class="form-label">Number of Passengers</label>
                        <input type="number" name="passengers" id="passengers" class="form-control" min="1" max="100" placeholder="Enter the number of passengers" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Next</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS for functionality -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>