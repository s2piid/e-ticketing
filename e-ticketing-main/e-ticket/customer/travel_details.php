<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');

// Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

// Show error message if 'error' query parameter is present
if (isset($_GET['error']) && $_GET['error'] == 'missing_booking_details') {
    echo '<div class="alert alert-danger" role="alert">
            Please complete the booking form before proceeding.
          </div>';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Store user selections in the session
    $_SESSION['departure'] = $_POST['departure'];
    $_SESSION['destination'] = $_POST['destination'];
    $_SESSION['departure_date'] = $_POST['departure_date'];
    $_SESSION['passengers'] = intval($_POST['passengers']); // Ensure passengers are an integer
    
    // Redirect to the ferry selection page
    header("Location: ferry_selection.php");
    exit();
}

// Fetch unique departure ports
$departureQuery = "SELECT DISTINCT departure_port FROM ferry_schedule WHERE status = 'active'";
$departureResult = $conn->query($departureQuery);

// Fetch unique arrival ports
$arrivalQuery = "SELECT DISTINCT arrival_port FROM ferry_schedule WHERE status = 'active'";
$arrivalResult = $conn->query($arrivalQuery);

$pageTitle = "Travel Details";
include 'header.php';
?>

<section class="container mt-5">
    <h2 class="text-center mb-4">Provide Your Travel Details</h2>
    <form method="POST" class="card shadow p-4">
        <div class="row g-3">
            <!-- Departure Port -->
            <div class="col-md-6">
                <label for="departure" class="form-label">Departure Port</label>
                <select id="departure" name="departure" class="form-select" required>
                    <option value="" disabled selected>Select Departure Port</option>
                    <?php while ($row = $departureResult->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($row['departure_port']) ?>">
                            <?= htmlspecialchars($row['departure_port']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <!-- Arrival Port -->
            <div class="col-md-6">
                <label for="destination" class="form-label">Arrival Port</label>
                <select id="destination" name="destination" class="form-select" required>
                    <option value="" disabled selected>Select Arrival Port</option>
                    <?php while ($row = $arrivalResult->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($row['arrival_port']) ?>">
                            <?= htmlspecialchars($row['arrival_port']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <div class="row g-3 mt-3">
            <!-- Date of Departure -->
            <div class="col-md-6">
                <label for="departure_date" class="form-label">Date of Departure</label>
                <input type="date" id="departure_date" name="departure_date" class="form-control" required>
            </div>
            
            <!-- Number of Passengers -->
            <div class="col-md-6">
                <label for="passengers" class="form-label">Number of Passengers</label>
                <input type="number" id="passengers" name="passengers" min="1" class="form-control" required>
            </div>
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary px-4">Next</button>
        </div>
    </form>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
