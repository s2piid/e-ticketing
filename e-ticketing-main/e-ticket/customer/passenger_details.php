<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');
// Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}
// Ensure the session has necessary data
if (!isset($_SESSION['departure']) || !isset($_SESSION['destination']) || !isset($_SESSION['departure_date']) || !isset($_SESSION['passengers'])) {
    echo "Session data is missing. Please go back and fill out the travel details.";
    exit();
}
// Fetch ferry id from session
$ferry_id = $_SESSION['selected_ferry'] ?? null;
$accom_id = $_SESSION['selected_accommodation'] ?? null;
if (!$ferry_id) {
    echo "Ferry selection is missing. Please go back and select a ferry.";
    exit();
}
// If the form is submitted, store the passenger details in the session
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['first_name'])) {
    $passenger_details = [];
    for ($i = 0; $i < count($_POST['first_name']); $i++) {
        // Sanitize and trim inputs
        $first_name = htmlspecialchars(trim($_POST['first_name'][$i]));
        $middle_name = isset($_POST['middle_name'][$i]) ? htmlspecialchars(trim($_POST['middle_name'][$i])) : null;
        $last_name = htmlspecialchars(trim($_POST['last_name'][$i]));
        $gender = $_POST['gender'][$i];
        $birth_date = $_POST['birth_date'][$i];
        $civil_status = $_POST['civil_status'][$i];
        $nationality = htmlspecialchars(trim($_POST['nationality'][$i]));
        $address = htmlspecialchars(trim($_POST['address'][$i]));
        $passenger_type = $_POST['passenger_type'][$i];
        // Handle file upload for ID photo
        $id_photo_path = null;
        if (isset($_FILES['id_photo']['name'][$i]) && $_FILES['id_photo']['name'][$i] != '') {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES['id_photo']['name'][$i]);
            
            // Validate if file is an image
            if (getimagesize($_FILES['id_photo']['tmp_name'][$i]) !== false) {
                if (move_uploaded_file($_FILES['id_photo']['tmp_name'][$i], $target_file)) {
                    $id_photo_path = $target_file; // Store the file path
                } else {
                    echo "Error uploading file.";
                }
            } else {
                echo "File is not an image.";
            }
        }
        // Store the passenger details
        $passenger_details[] = [
            'first_name' => $first_name,
            'middle_name' => $middle_name,
            'last_name' => $last_name,
            'gender' => $gender,
            'birth_date' => $birth_date,
            'civil_status' => $civil_status,
            'nationality' => $nationality,
            'address' => $address,
            'passenger_type' => $passenger_type,
            'id_photo' => $id_photo_path, // Store the path of the uploaded file
        ];
    }
    $_SESSION['passenger_details'] = $passenger_details;
    // Debugging: Check what data is being stored
    echo "<pre>";
    var_dump($_SESSION['passenger_details']);
    echo "</pre>";
    
    // Redirect to review booking page after storing the details
    header("Location: review_booking.php");
    exit();
}
// Fetch data from session
$departure = $_SESSION['departure'];
$destination = $_SESSION['destination'];
$departure_date = $_SESSION['departure_date'];
$passengers = $_SESSION['passengers'];
// Fetch ferry name based on selected ferry
$sql_ferry = "SELECT ferry_name FROM ferries WHERE ferry_id = ?";
$stmt_ferry = $conn->prepare($sql_ferry);
$stmt_ferry->bind_param("i", $ferry_id);
$stmt_ferry->execute();
$stmt_ferry->bind_result($ferry_name);
$stmt_ferry->fetch();
$stmt_ferry->close();
// Fetch accommodation details (e.g., price)
$sql_accom = "SELECT a.accom_type, ap.price 
              FROM accommodation_prices ap
              JOIN accommodation a ON ap.accom_id = a.accom_price_id
              WHERE ap.ferry_id = ? AND ap.accom_id = ?";
$stmt_accom = $conn->prepare($sql_accom);
$stmt_accom->bind_param("ii", $ferry_id, $_SESSION['selected_accommodation']);
$stmt_accom->execute();
$stmt_accom->bind_result($accom_type, $price);
$stmt_accom->fetch();
$stmt_accom->close();
$pageTitle = "Passenger Details";
include 'header.php';
?>
<div class="container mt-5 mb-5">
    <div class="booking-card">
        <!-- Booking Summary -->
        <div class="booking-summary mb-4">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="summary-card">
                        <i class="fas fa-ship summary-icon"></i>
                        <h6>Selected Ferry</h6>
                        <p class="mb-0"><?= htmlspecialchars($ferry_name) ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card">
                        <i class="fas fa-map-marker-alt summary-icon"></i>
                        <h6>Route</h6>
                        <p class="mb-0"><?= htmlspecialchars($departure) ?> → <?= htmlspecialchars($destination) ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card">
                        <i class="fas fa-calendar-alt summary-icon"></i>
                        <h6>Departure Date</h6>
                        <p class="mb-0"><?= htmlspecialchars($departure_date) ?></p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Passenger Form -->
        <form method="POST" action="passenger_details.php" enctype="multipart/form-data" id="passengerForm">
            <div class="passenger-tabs-container">
                <!-- Tab Navigation -->
                <div class="passenger-tabs">
                    <?php for ($i = 1; $i <= $passengers; $i++): ?>
                        <button type="button" 
                                class="passenger-tab <?= $i === 1 ? 'active' : '' ?>"
                                data-tab="passenger_<?= $i ?>">
                            <span class="tab-number"><?= $i ?></span>
                            <span class="tab-text">Passenger <?= $i ?></span>
                        </button>
                    <?php endfor; ?>
                </div>
                <!-- Tab Content -->
                <div class="passenger-forms">
                    <?php for ($i = 1; $i <= $passengers; $i++): ?>
                        <div class="passenger-form <?= $i === 1 ? 'active' : '' ?>" id="passenger_<?= $i ?>">
                            <div class="form-section">
                                <h4 class="section-title">Personal Information</h4>
                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="first_name_<?= $i ?>" 
                                                   name="first_name[]" 
                                                   placeholder="First Name" 
                                                   required>
                                            <label for="first_name_<?= $i ?>">First Name</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="middle_name_<?= $i ?>" 
                                                   name="middle_name[]" 
                                                   placeholder="Middle Name">
                                            <label for="middle_name_<?= $i ?>">Middle Name</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="last_name_<?= $i ?>" 
                                                   name="last_name[]" 
                                                   placeholder="Last Name" 
                                                   required>
                                            <label for="last_name_<?= $i ?>">Last Name</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-4 mt-3">
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <select class="form-select" 
                                                    id="gender_<?= $i ?>" 
                                                    name="gender[]" 
                                                    required>
                                                <option value="">Select Gender</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                            <label for="gender_<?= $i ?>">Gender</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="date" 
                                                   class="form-control" 
                                                   id="birth_date_<?= $i ?>" 
                                                   name="birth_date[]" 
                                                   required>
                                            <label for="birth_date_<?= $i ?>">Birth Date</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <select class="form-select" 
                                                    id="civil_status_<?= $i ?>" 
                                                    name="civil_status[]" 
                                                    required>
                                                <option value="">Select Status</option>
                                                <option value="Single">Single</option>
                                                <option value="Married">Married</option>
                                                <option value="Widowed">Widowed</option>
                                                <option value="Divorced">Divorced</option>
                                            </select>
                                            <label for="civil_status_<?= $i ?>">Civil Status</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-section mt-4">
                                <h4 class="section-title">Additional Information</h4>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="nationality_<?= $i ?>" 
                                                   name="nationality[]" 
                                                   placeholder="Nationality" 
                                                   required>
                                            <label for="nationality_<?= $i ?>">Nationality</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="address_<?= $i ?>" 
                                                   name="address[]" 
                                                   placeholder="Address" 
                                                   required>
                                            <label for="address_<?= $i ?>">Address</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-4 mt-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select" 
                                                    id="passenger_type_<?= $i ?>" 
                                                    name="passenger_type[]" 
                                                    required>
                                                <option value="">Select Type</option>
                                                <option value="regular">Regular</option>
                                                <option value="student">Student</option>
                                                <option value="child">Child</option>
                                                <option value="pwd">PWD</option>
                                                <option value="infant">Infant</option>
                                                <option value="senior">Senior Citizen</option>
                                            </select>
                                            <label for="passenger_type_<?= $i ?>">Passenger Type</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="file" 
                                                   class="form-control" 
                                                   id="id_photo_<?= $i ?>" 
                                                   name="id_photo[]" 
                                                   accept="image/*">
                                            <label for="id_photo_<?= $i ?>">ID Photo</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="form-navigation mt-4">
                <button type="button" class="btn btn-secondary" id="prevBtn" disabled>
                    <i class="fas fa-arrow-left me-2"></i>Previous
                </button>
                <button type="button" class="btn btn-primary" id="nextBtn">
                    Next<i class="fas fa-arrow-right ms-2"></i>
                </button>
                <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                    Review Booking<i class="fas fa-check ms-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>
<style>
.booking-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    padding: 2rem;
}
.booking-summary {
    background: linear-gradient(135deg, #f6f8fb 0%, #e9eef5 100%);
    border-radius: 15px;
    padding: 1.5rem;
}
.summary-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    height: 100%;
}
.summary-icon {
    font-size: 2rem;
    color: #2563eb;
    margin-bottom: 1rem;
}
.passenger-tabs-container {
    margin-top: 2rem;
}
.passenger-tabs {
    display: flex;
    gap: 1rem;
    overflow-x: auto;
    padding-bottom: 1rem;
    margin-bottom: 2rem;
}
.passenger-tab {
    background: none;
    border: 2px solid #e5e7eb;
    border-radius: 50px;
    padding: 0.75rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: fit-content;
}
.passenger-tab.active {
    background: #2563eb;
    color: white;
    border-color: #2563eb;
}
.tab-number {
    background: rgba(255, 255, 255, 0.2);
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}
.passenger-form {
    display: none;
    animation: fadeIn 0.3s ease;
}
.passenger-form.active {
    display: block;
}
.form-section {
    background: #f8fafc;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 1.5rem;
}
.section-title {
    color: #1e293b;
    margin-bottom: 1.5rem;
    font-size: 1.25rem;
    font-weight: 600;
}
.form-navigation {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
}
.form-navigation button {
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    font-weight: 500;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
@media (max-width: 768px) {
    .booking-card {
        padding: 1rem;
    }
    
    .form-section {
        padding: 1rem;
    }
    
    .passenger-tabs {
        flex-wrap: nowrap;
        margin: -1rem -1rem 1rem -1rem;
        padding: 1rem;
    }
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.passenger-form');
    const tabs = document.querySelectorAll('.passenger-tab');
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');
    let currentTab = 0;
    function showTab(n) {
        forms.forEach(form => form.classList.remove('active'));
        tabs.forEach(tab => tab.classList.remove('active'));
        
        forms[n].classList.add('active');
        tabs[n].classList.add('active');
        
        prevBtn.disabled = n === 0;
        
        if (n === forms.length - 1) {
            nextBtn.style.display = 'none';
            submitBtn.style.display = 'block';
        } else {
            nextBtn.style.display = 'block';
            submitBtn.style.display = 'none';
        }
    }
    function validateForm() {
        const activeForm = forms[currentTab];
        const inputs = activeForm.querySelectorAll('input[required], select[required]');
        let valid = true;
        
        inputs.forEach(input => {
            if (!input.value) {
                input.classList.add('is-invalid');
                valid = false;
            } else {
                input.classList.remove('is-invalid');
            }
        });
        
        return valid;
    }
    nextBtn.addEventListener('click', function() {
        if (validateForm()) {
            currentTab++;
            showTab(currentTab);
        }
    });
    prevBtn.addEventListener('click', function() {
        currentTab--;
        showTab(currentTab);
    });
    tabs.forEach((tab, index) => {
        tab.addEventListener('click', () => {
            if (validateForm()) {
                currentTab = index;
                showTab(currentTab);
            }
        });
    });
    // Initialize form validation styles
    const form = document.getElementById('passengerForm');
    form.classList.add('needs-validation');
    form.setAttribute('novalidate', '');
    showTab(currentTab);
});
</script>
<?php include 'footer.php'; ?>