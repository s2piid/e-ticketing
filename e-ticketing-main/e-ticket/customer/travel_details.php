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
    if (!isset($_POST['agree_terms'])) {
        echo '<div class="alert alert-danger text-center mt-4">
                You must agree to the terms and conditions before proceeding.
              </div>';
    } else {
        // Store user selections in the session
        $_SESSION['departure'] = $_POST['departure'];
        $_SESSION['destination'] = $_POST['destination'];
        $_SESSION['departure_date'] = $_POST['departure_date'];
        $_SESSION['passengers'] = intval($_POST['passengers']); // Ensure passengers are an integer
        
        // Redirect to the ferry selection page
        header("Location: ferry_selection.php");
        exit();
    }
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

<style>
    body {
        background: #f0f9ff;
        font-family: 'Inter', sans-serif;
    }
    
    .container {
        max-width: 800px;
        margin: 50px auto;
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    .card {
        background: #f7fafc;
        border: none;
    }

    .card h2 {
        color: #1e40af;
    }

    .form-label {
        font-weight: bold;
        color: #1e40af;
    }

    .form-select, .form-control {
        border-radius: 5px;
        box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.1);
    }

    .btn-primary {
        background: linear-gradient(135deg, #3b82f6, #60a5fa);
        border: none;
        transition: background 0.3s;
        padding: 12px 30px;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #1e40af, #3b82f6);
    }

    .btn-secondary {
        background: #e2e8f0;
        border: none;
        transition: background 0.3s;
        padding: 12px 30px;
    }

    .btn-secondary:hover {
        background: #cbd5e0;
    }

    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
        margin-bottom: 20px;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background-color: #fefefe;
        margin: auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 500px;
        border-radius: 10px;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    /* Make checkbox and terms text more visible */
    .form-check {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 20px;
    }

    .form-check input {
        width: 25px;
        height: 25px;
        margin-right: 10px;
    }

    .form-check-label {
        font-size: 16px;
        color: #1e40af;
    }

    .form-check-label a {
        color: #3b82f6;
    }

    /* Style the back button to be more visible and position it beside the next button */
    .btn-group {
        display: flex;
        justify-content: center;
        gap: 20px;
    }

    .btn-secondary {
        font-size: 16px;
        padding: 15px 40px;
    }
    /* Both buttons now use the same design */
    .btn-primary {
        background: linear-gradient(135deg, #3b82f6, #60a5fa);
        border: none;
        transition: background 0.3s;
        padding: 12px 30px;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #1e40af, #3b82f6);
    }

    /* Back button uses the same style as Next */
    .btn-secondary {
        background: linear-gradient(135deg, #3b82f6, #60a5fa);
        border: none;
        transition: background 0.3s;
        padding: 12px 30px;
    }

    .btn-secondary:hover {
        background: linear-gradient(135deg, #1e40af, #3b82f6);
    }
</style>

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

        <!-- Agreement Checkbox -->
        <div class="form-check">
            <input type="checkbox" id="agree_terms" name="agree_terms" class="form-check-input" required>
            <label for="agree_terms" class="form-check-label">
                I agree to the <a href="#" id="termsLink">terms and conditions</a>
            </label>
        </div>

        <!-- Button Group with Back and Next Buttons -->
        <div class="btn-group mt-4">
            <a href="customer_dashboard.php" class="btn btn-primary px-4">Back</a>
            <button type="submit" class="btn btn-primary px-4" id="submitBtn" disabled>Next</button>
        </div>
    </form>
</section>

<!-- The Modal -->
<div id="termsModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Terms and Conditions</h2>
        <h2>Terms and Conditions</h2>
        <p>By filling out the booking form, you agree to provide accurate and complete personal information, including but not limited to your full name, date of birth, gender, contact information (phone number and email), and any other required details for processing the booking.</p>
        <p>The personal information you provide will be used solely for processing your ferry booking, communication about your booking status, and any necessary follow-ups. We may also use your information to send you marketing materials or updates about our services, provided that you have consented to receive such communications.</p>
        <p>Your personal information will be stored securely, and appropriate measures will be taken to prevent unauthorized access, alteration, or disclosure. We will not share your personal information with third parties unless required by law or for completing your booking.</p>
        <p>You must ensure that all the information provided is accurate and up-to-date. Failure to do so may result in an incomplete booking or delay in processing your reservation.</p>
        <p>By proceeding with the booking process, you acknowledge that you have read, understood, and agreed to these terms.</p>
    </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('termsLink').addEventListener('click', function(event) {
    event.preventDefault();
    document.getElementById('termsModal').style.display = "flex";
});

document.querySelector('.close').addEventListener('click', function() {
    document.getElementById('termsModal').style.display = "none";
});

// Enable the submit button only when the checkbox is checked
document.querySelector('input[type="checkbox"]').addEventListener('change', function() {
    document.querySelector('button[type="submit"]').disabled = !this.checked;
});
</script>

<?php include 'footer.php'; ?>
