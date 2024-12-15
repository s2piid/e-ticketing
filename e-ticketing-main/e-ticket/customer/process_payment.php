<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');

// Ensure the user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

if (!isset(
    $_SESSION['selected_ferry'],
    $_SESSION['selected_accommodation'],
    $_SESSION['passenger_details'],
    $_SESSION['departure'],
    $_SESSION['destination'],
    $_SESSION['departure_date']
)) {
    die("Error: Booking details are missing. Please try again.");
}

// Retrieve session data
$passenger_details = $_SESSION['passenger_details'];
$customer_id = (int)$_SESSION['customer_id'];
$ferry_id = (int)$_SESSION['selected_ferry'];
$departure = $_SESSION['departure'];
$destination = $_SESSION['destination'];
$departure_date = $_SESSION['departure_date'];
$accom_price = (float)$_SESSION['selected_accommodation']['price'];

// Discount rates
$discount_rates = [
    'regular' => 0,
    'student' => 0.2,
    'pwd' => 0.2,
    'senior' => 0.2,
    'child' => 0.5,
    'infant' => 1
];

$error_message = $success_message = '';
$upload_dir = 'uploads/'; // Directory where the images will be stored
$valid_id_paths = [];

// Ensure the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reference_number'])) {
    $reference_number = trim($_POST['reference_number']);

    // Validate reference number
    if (!preg_match('/^\d{13}$/', $reference_number)) {
        $error_message = "Invalid GCash reference number. It must be 13 digits.";
    } else {
        // Handle file uploads
        foreach ($passenger_details as $index => $passenger) {
            if (isset($_FILES['id_photo_' . $index]) && $_FILES['id_photo_' . $index]['error'] == 0) {
                // File validation
                $file_tmp = $_FILES['id_photo_' . $index]['tmp_name'];
                $file_name = $_FILES['id_photo_' . $index]['name'];
                $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array(strtolower($file_ext), $allowed_extensions)) {
                    $file_path = $upload_dir . uniqid('img_', true) . '.' . $file_ext;
                    if (move_uploaded_file($file_tmp, $file_path)) {
                        $valid_id_paths[] = $file_path;
                    } else {
                        $error_message = "Error uploading image for passenger " . ($index + 1);
                        break;
                    }
                } else {
                    $error_message = "Invalid file type for passenger " . ($index + 1);
                    break;
                }
            } else {
                $valid_id_paths[] = null; // No file uploaded for this passenger
            }
        }

        // Check if reference number is unique
        $check_ref = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE reference_number = ?");
        $check_ref->bind_param("s", $reference_number);
        $check_ref->execute();
        $check_ref->bind_result($count);
        $check_ref->fetch();
        $check_ref->close();

        if ($count > 0) {
            $error_message = "This reference number is already used.";
        } else {
            // Calculate total cost and insert booking data
            try {
                $conn->begin_transaction();
                $stmt = $conn->prepare("
                    INSERT INTO bookings (
                        fk_user_id, fk_ferry_id, departure, destination, departure_date,
                        first_name, middle_name, last_name, gender, birth_date, nationality,
                        civil_status, address, passenger_type, valid_id, accom_price, discount, total_cost, booking_date, status, reference_number
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)
                ");
                $status = 'pending';
                $total_cost = 0;

                foreach ($passenger_details as $index => $passenger) {
                    $discount_rate = ($discount_rates[strtolower($passenger['passenger_type'])] ?? 0);
                    $valid_id = $valid_id_paths[$index] ?? null; // Get the uploaded image path or null
                    $total_cost += $accom_price * (1 - $discount_rate);

                    $stmt->bind_param(
                        "iissssssssssssddssss",
                        $customer_id, $ferry_id, $departure, $destination, $departure_date,
                        $passenger['first_name'], $passenger['middle_name'], $passenger['last_name'],
                        $passenger['gender'], $passenger['birth_date'], $passenger['nationality'],
                        $passenger['civil_status'], $passenger['address'], $passenger['passenger_type'],
                        $valid_id, $accom_price, $discount_rate, $total_cost,
                        $status, $reference_number
                    );

                    if (!$stmt->execute()) {
                        throw new Exception("Insert failed: " . $stmt->error);
                    }
                }

                $conn->commit();
                unset($_SESSION['selected_ferry'], $_SESSION['selected_accommodation'], $_SESSION['passenger_details']);
                $success_message = "Booking successfully submitted! Your Reference Number: $reference_number.";
            } catch (Exception $e) {
                $conn->rollback();
                $error_message = "Error processing your booking. Please contact support.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <?php if ($error_message): ?>
        <div class="modal fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Error</h5>
                    </div>
                    <div class="modal-body">
                        <p><?= htmlspecialchars($error_message); ?></p>
                    </div>
                    <div class="modal-footer">
                        <a href="payment_details.php" class="btn btn-secondary">Go Back</a>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif ($success_message): ?>
        <div class="modal fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Booking Success</h5>
                    </div>
                    <div class="modal-body">
                        <p><?= htmlspecialchars($success_message); ?></p>
                    </div>
                    <div class="modal-footer">
                        <a href="customer_dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
