<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');

// Redirect to login if the admin is not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

try {
    // Fetch the admin's username and email securely
    $stmt = $conn->prepare("SELECT username, email FROM Users WHERE user_id = ?");
    if (!$stmt) {
        throw new Exception("Database query preparation failed: " . $conn->error);
    }

    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $stmt->bind_result($admin_username, $admin_email);

    if (!$stmt->fetch()) {
        // Log error and redirect to login
        error_log("Invalid admin_id: " . $admin_id);
        session_destroy();
        header("Location: admin_login.php");
        exit();
    }

    $stmt->close();
} catch (Exception $e) {
    // Log exception details and terminate
    error_log($e->getMessage());
    die("An unexpected error occurred. Please try again later.");
}
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
            transform: scale(1.05);
        }

        .welcome-section {
    background: white;
    border-radius: 1rem;
    padding: 1rem; /* Reduced padding */
    margin: 2rem 0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.welcome-section h2 {
    font-size: 1.5rem; /* Reduced font size */
    margin-bottom: 0.5rem; /* Reduced margin */
}

.welcome-section p {
    font-size: 0.9rem; /* Reduced font size for the email */
    color: #666;
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
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); /* Adjusted min width for better alignment */
    gap: 1rem;
    margin-top: 2rem;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr); /* Set fixed 5 columns */
    gap: 1rem;
    margin-top: 2rem;
}

.dashboard-card {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
    text-align: center;
    box-sizing: border-box;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    background: white;
    padding: 1rem;
    border-radius: 1rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    height: 100%;
}

.dashboard-card .card-icon {
    width: 60px;
    height: 60px;
    background: var(--background-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    color: var(--primary-color);
    font-size: 1.8rem;
}

.dashboard-card .card-title {
    color: var(--primary-color);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.dashboard-card .card-text {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.dashboard-card .btn-custom {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 0.5rem 1.5rem;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
    margin-top: 1rem;
    font-size: 1rem;
}

.dashboard-card .btn-custom:hover {
    background: var(--secondary-color);
    transform: translateY(-2px) scale(1.05);
}

.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
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
                            <a class="nav-link" href="admin_login.php"><i class="fas fa-sign-out-alt me-2"></i>Sign Out</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="welcome-section">
            <div class="user-avatar">
                <i class="fas fa-user"></i>
            </div>
            <h2 class="mb-3">Welcome, <?php echo htmlspecialchars($admin_username); ?>!</h2>
            <p class="text-muted"><?php echo htmlspecialchars($admin_email); ?></p>
        </div>

        <div class="dashboard-grid">
    <div class="dashboard-card">
        <div class="card-icon">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <h3 class="card-title">Schedule & Rates</h3>
        <p class="card-text">Manage ferry schedules and ticket rates</p>
        <button onclick="window.location.href='schedule_and_rates.php'" class="btn btn-custom">View Schedule</button>
    </div>

    <div class="dashboard-card">
        <div class="card-icon">
            <i class="fas fa-ticket-alt"></i>
        </div>
        <h3 class="card-title">Manage Users</h3>
        <p class="card-text">Edit and Delete Users</p>
        <button onclick="window.location.href='manage_users.php'" class="btn btn-custom">Manage Users</button>
    </div>

    <div class="dashboard-card">
        <div class="card-icon">
            <i class="fas fa-history"></i>
        </div>
        <h3 class="card-title">Bookings</h3>
        <p class="card-text">View and manage your previous bookings</p>
        <button onclick="window.location.href='view_reservation.php'" class="btn btn-custom">View Bookings</button>
    </div>

    <div class="dashboard-card">
        <div class="card-icon">
            <i class="fas fa-user-edit"></i>
        </div>
        <h3 class="card-title">Manage Ferry and Schedules</h3>
        <p class="card-text">Update and Delete Ferry Schedules</p>
        <button onclick="window.location.href='manage_ferry.php'" class="btn btn-custom">Manage Ferry</button>
    </div>

    <div class="dashboard-card">
        <div class="card-icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <h3 class="card-title">Reports</h3>
        <p class="card-text">View Reports</p>
        <button onclick="window.location.href='reports.php'" class="btn btn-custom">View Reports</button>
    </div>
</div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
