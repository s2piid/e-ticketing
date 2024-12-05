<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');

if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

$user_id = $_SESSION['customer_id'];
$stmt = $conn->prepare("SELECT username, email FROM Users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - Ferry E-Ticketing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1e40af;
            --secondary-color: #3b82f6;
            --accent-color: #60a5fa;
            --background-color: #f0f9ff;
        }

        body {
            background: var(--background-color);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 1rem 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand img {
            height: 50px;
            object-fit: contain;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white !important;
        }

        .welcome-section {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            margin: 2rem 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .user-avatar {
            width: 100px;
            height: 100px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: white;
            font-size: 2.5rem;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .dashboard-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .card-icon {
            width: 60px;
            height: 60px;
            background: var(--background-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: var(--primary-color);
            font-size: 1.5rem;
        }

        .card-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .card-text {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .btn-custom {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .navbar-nav {
                background: rgba(255, 255, 255, 0.1);
                border-radius: 0.5rem;
                padding: 1rem;
                margin-top: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <img src="gabisan2.jpg" alt="Logo">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="home.php"><i class="fas fa-home me-2"></i>Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="services.php"><i class="fas fa-ship me-2"></i>Services</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact.php"><i class="fas fa-envelope me-2"></i>Contact</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="news.php"><i class="fas fa-newspaper me-2"></i>News</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="signout.php"><i class="fas fa-sign-out-alt me-2"></i>Sign Out</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="welcome-section">
            <div class="user-avatar">
                <i class="fas fa-user"></i>
            </div>
            <h2 class="mb-3">Welcome back, <?php echo htmlspecialchars($username); ?>!</h2>
            <p class="text-muted"><?php echo htmlspecialchars($email); ?></p>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3 class="card-title">Schedule & Rates</h3>
                <p class="card-text">View ferry schedules and current ticket rates</p>
                <button onclick="window.location.href='schedule_and_rate.php'" class="btn btn-custom">View Schedule</button>
            </div>

            <div class="dashboard-card">
                <div class="card-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <h3 class="card-title">Book Now</h3>
                <p class="card-text">Reserve your ferry tickets quickly and easily</p>
                <button onclick="window.location.href='travel_details.php'" class="btn btn-custom">Book Ticket</button>
            </div>

            <div class="dashboard-card">
                <div class="card-icon">
                    <i class="fas fa-history"></i>
                </div>
                <h3 class="card-title">Booking History</h3>
                <p class="card-text">View and manage your previous bookings</p>
                <button onclick="window.location.href='book_history.php'" class="btn btn-custom">View History</button>
            </div>

            <div class="dashboard-card">
                <div class="card-icon">
                    <i class="fas fa-user-edit"></i>
                </div>
                <h3 class="card-title">Profile Settings</h3>
                <p class="card-text">Update your profile information and password</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button onclick="window.location.href='update_pro.php'" class="btn btn-custom">Update Profile</button>
                    <button onclick="window.location.href='change_pass.php'" class="btn btn-custom">Change Password</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>