<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');

// Check if customer is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

// Check if essential session data exists
if (!isset($_SESSION['departure']) || !isset($_SESSION['destination']) || !isset($_SESSION['departure_date']) || !isset($_SESSION['passengers'])) {
    header("Location: travel_details.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ferry_id = $_POST['ferry_id'];
    $accom_id = $_POST['accom_id'];

    // Validate ferry and accommodation selection
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS valid_count
        FROM ferry_schedule fs
        LEFT JOIN accommodation_prices ap ON fs.ferry_id = ap.ferry_id
        WHERE fs.ferry_id = ? AND ap.accom_id = ?
    ");
    $stmt->bind_param("ii", $ferry_id, $accom_id); // Use integer for binding
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['valid_count'] === 0) {
        echo "<div class='alert alert-danger text-center mt-4'>Invalid selection. Please try again.</div>";
        exit();
    }

    // Store selected ferry and accommodation in session
    $_SESSION['selected_ferry'] = $ferry_id;
    $_SESSION['selected_accommodation'] = $accom_id;

    // // Debugging: Check if session values are set correctly
    // var_dump($_SESSION);
    // // exit(); // Stop execution here to check session

    // Redirect to passenger details page
    header("Location: passenger_details.php");
    exit();
}

$departure = $_SESSION['departure'];
$destination = $_SESSION['destination'];

// Fetch ferries and accommodations for the selected route
$query = "
    SELECT 
        fs.ferry_id,
        f.ferry_name,
        a.accom_price_id AS accom_id,
        a.accom_type,
        ap.price
    FROM ferry_schedule fs
    LEFT JOIN ferries f ON fs.ferry_id = f.ferry_id
    LEFT JOIN accommodation_prices ap ON fs.ferry_id = ap.ferry_id
    LEFT JOIN accommodation a ON ap.accom_id = a.accom_price_id
    WHERE fs.departure_port = ? 
      AND fs.arrival_port = ? 
      AND fs.status = 'active'
    ORDER BY fs.ferry_id, a.accom_type;
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $departure, $destination);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Check if ferries are available
if ($result->num_rows === 0) {
    echo "<div class='alert alert-warning text-center mt-4'>No ferries or accommodations available for the selected route. Please try a different route.</div>";
    echo "<div class='text-center'><a href='travel_details.php' class='btn btn-secondary mt-3'>Go Back</a></div>";
    include 'footer.php'; // Ensure HTML footer
    exit();
}

$pageTitle = "Select Ferry and Accommodation";
include 'header.php';
?>

<section class="container mt-5">
    <h2 class="text-center mb-4">Select Your Ferry and Accommodation</h2>
    <form method="POST" class="card shadow p-4">
        <!-- Ferry Selection -->
        <div class="mb-4">
            <label for="ferry_id" class="form-label">Select Ferry</label>
            <select id="ferry_id" name="ferry_id" class="form-select" required>
                <option value="" disabled selected>Select a Ferry</option>
                <?php
                // Display ferries and accommodations
                $ferries = [];
                while ($row = $result->fetch_assoc()) {
                    $ferry_id = htmlspecialchars($row['ferry_id']);
                    $ferry_name = htmlspecialchars($row['ferry_name']);
                    $accom_id = htmlspecialchars($row['accom_id']);
                    $accom_type = htmlspecialchars($row['accom_type']);
                    $price = number_format($row['price'], 2);

                    if (!isset($ferries[$ferry_id])) {
                        $ferries[$ferry_id] = [
                            'name' => $ferry_name,
                            'accommodations' => []
                        ];
                    }

                    $ferries[$ferry_id]['accommodations'][] = [
                        'id' => $accom_id,
                        'type' => $accom_type,
                        'price' => $price
                    ];
                }

                foreach ($ferries as $ferry_id => $ferry) {
                    echo "<option value='{$ferry_id}'>{$ferry['name']}</option>";
                }
                ?>
            </select>
        </div>

        <!-- Accommodation Selection -->
        <div class="mb-4">
            <label for="accom_id" class="form-label">Select Accommodation</label>
            <select id="accom_id" name="accom_id" class="form-select" required>
    <option value="" disabled selected>Select Accommodation</option>
    <?php
    foreach ($ferries as $ferry_id => $ferry) {
        foreach ($ferry['accommodations'] as $accommodation) {
            $accom_type = htmlspecialchars($accommodation['type']);
            $accom_price = number_format($accommodation['price'], 2);
            echo "<option class='accom_option ferry_{$ferry_id}' value='{$accommodation['id']}' 
                data-ferry-id='{$ferry_id}' style='display: none;'>$accom_type - ₱$accom_price</option>";
        }
    }
    ?>
</select>
        </div>

        <div class="text-center mt-4">
            <button type="submit" id="nextBtn" class="btn btn-primary px-4" disabled>Next</button>
        </div>
    </form>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('ferry_id').addEventListener('change', function () {
    var ferry_id = this.value;
    var accomOptions = document.querySelectorAll('.accom_option');

    accomOptions.forEach(function (option) {
        option.style.display = 'none';
        option.removeAttribute('selected');
    });

    var selectedFerryOptions = document.querySelectorAll('.ferry_' + ferry_id);
    if (selectedFerryOptions.length === 1) {
        selectedFerryOptions[0].style.display = 'block';
        selectedFerryOptions[0].setAttribute('selected', 'selected');
    } else {
        selectedFerryOptions.forEach(function (option) {
            option.style.display = 'block';
        });
    }

    toggleNextButton();
});

document.getElementById('accom_id').addEventListener('change', toggleNextButton);

function toggleNextButton() {
    const ferrySelected = document.getElementById('ferry_id').value !== '';
    const accomSelected = document.getElementById('accom_id').value !== '';
    document.getElementById('nextBtn').disabled = !(ferrySelected && accomSelected);
}
</script>
</body>
</html>
