<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php'); // Ensure the correct path

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
$accom_stmt->bind_param("ss", $departure, $destination);a
$accom_stmt->execute();
$accom_result = $accom_stmt->get_result();
$accom_stmt->close();

// Group accommodations by ferry_id
$accommodations = [];
while ($accom_row = $accom_result->fetch_assoc()) {
    $accommodations[$accom_row['ferry_id']][] = $accom_row;
}

// Encode accommodations data for JavaScript
$accommodations_json = json_encode($accommodations);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --accent-color: #3b82f6;
            --background-color: #f8fafc;
            --card-background: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
        }

        body {
            background-color: var(--background-color);
            color: var(--text-primary);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .main-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .booking-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .booking-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .summary-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 0.5rem;
        }

        .card {
            background: var(--card-background);
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-color);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .card-header {
            background: var(--primary-color);
            color: white;
            padding: 1rem;
            font-weight: 600;
        }

        .form-label {
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
        }

        .passenger-card {
            position: relative;
            padding: 1.5rem;
        }

        .passenger-number {
            position: absolute;
            top: -1rem;
            left: 1rem;
            background: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.875rem;
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 1rem 2rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--secondary-color);
            transform: translateY(-1px);
        }

        .payment-summary {
            background: var(--card-background);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .payment-summary h4 {
            color: var(--text-primary);
            margin-bottom: 1rem;
        }

        .price-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .total-price {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .booking-summary {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="main-container">
        <div class="booking-header">
            <h2 class="mb-4">Passenger Details</h2>
            <div class="booking-summary">
                <div class="summary-item">
                    <i class="fas fa-plane-departure me-2"></i>
                    <strong>From:</strong> <?= htmlspecialchars($departure) ?>
                </div>
                <div class="summary-item">
                    <i class="fas fa-plane-arrival me-2"></i>
                    <strong>To:</strong> <?= htmlspecialchars($destination) ?>
                </div>
                <div class="summary-item">
                    <i class="fas fa-calendar me-2"></i>
                    <strong>Date:</strong> <?= htmlspecialchars($departure_date) ?>
                </div>
            </div>
        </div>

        <form action="process_booking.php" method="POST" enctype="multipart/form-data">
            <!-- Travel Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-ship me-2"></i> Travel Details
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="ferry_id" class="form-label">Select Ferry</label>
                            <select name="ferry_id" id="ferry_id" class="form-select" required>
                                <option value="" disabled selected>Choose your ferry</option>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($row['ferry_id']) ?>"><?= htmlspecialchars($row['ferry_name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="accom_id" class="form-label">Select Accommodation</label>
                            <select name="accom_id" id="accom_id" class="form-select" required>
                                <option value="" disabled selected>Choose accommodation type</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Passenger Tabs -->
            <ul class="nav nav-tabs" id="passengerTabs" role="tablist">
                <?php for ($i = 1; $i <= $passengers; $i++): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $i === 1 ? 'active' : '' ?>" id="passenger-tab-<?= $i ?>" data-bs-toggle="tab" data-bs-target="#passenger-<?= $i ?>" type="button" role="tab" aria-controls="passenger-<?= $i ?>" aria-selected="<?= $i === 1 ? 'true' : 'false' ?>">
                            Passenger <?= $i ?>
                        </button>
                    </li>
                <?php endfor; ?>
            </ul>
            <div class="tab-content" id="passengerTabContent">
                <?php for ($i = 1; $i <= $passengers; $i++): ?>
                    <div class="tab-pane fade <?= $i === 1 ? 'show active' : '' ?>" id="passenger-<?= $i ?>" role="tabpanel" aria-labelledby="passenger-tab-<?= $i ?>">
                        <div class="card passenger-card mt-4">
                            <div class="row g-4">
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
                                <div class="col-md-6">
                                    <label class="form-label">Passenger Type</label>
                                    <select name="passenger_type[]" class="form-select">
                                        <option value="regular">Regular</option>
                                        <option value="student">Student</option>
                                        <option value="child">Child</option>
                                        <option value="pwd">PWD</option>
                                        <option value="infant">Infant</option>
                                        <option value="senior">Senior Citizen</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Upload Valid ID</label>
                                    <input type="file" name="id_photo[]" class="form-control" accept="image/*" required>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>

            <div class="card payment-summary mt-4">
                <h4><i class="fas fa-receipt me-2"></i>Payment Summary</h4>
                <div id="payment_details"></div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-check-circle me-2"></i>Complete Booking
                </button>
            </div>
        </form>
    </div>

    <script>
        const accommodations = <?= $accommodations_json; ?>;

        document.addEventListener('DOMContentLoaded', () => {
            const ferrySelect = document.getElementById('ferry_id');
            const accomSelect = document.getElementById('accom_id');
            const passengers = <?= $passengers ?>;
            const paymentDetails = document.getElementById('payment_details');

            const updateAccommodations = (ferryId) => {
                accomSelect.innerHTML = '<option value="" disabled selected>Choose accommodation type</option>';
                if (accommodations[ferryId]) {
                    accommodations[ferryId].forEach(accom => {
                        const option = document.createElement('option');
                        option.value = accom.accom_price_id;
                        option.dataset.price = accom.price;
                        option.textContent = `${accom.accom_type} - ₱${parseFloat(accom.price).toFixed(2)}`;
                        accomSelect.appendChild(option);
                    });
                }
                updatePaymentDetails(0);
            };

            const updatePaymentDetails = (price) => {
                const totalFare = price * passengers;
                paymentDetails.innerHTML = `
                    <div class="price-item">
                        <span>Accommodation Price:</span>
                        <span>₱${price.toFixed(2)}</span>
                    </div>
                    <div class="price-item total-price">
                        <span>Total Fare for All Passengers:</span>
                        <span>₱${totalFare.toFixed(2)}</span>
                    </div>
                `;
            };

            ferrySelect.addEventListener('change', () => {
                const selectedFerryId = ferrySelect.value;
                updateAccommodations(selectedFerryId);
            });

            accomSelect.addEventListener('change', () => {
                const selectedOption = accomSelect.options[accomSelect.selectedIndex];
                const price = parseFloat(selectedOption.dataset.price || 0);
                updatePaymentDetails(price);
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>