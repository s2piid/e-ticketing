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
    // Fetch the admin's username securely
    $stmt = $conn->prepare("SELECT username FROM Users WHERE user_id = ?");
    if (!$stmt) {
        throw new Exception("Database query preparation failed: " . $conn->error);
    }

    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $stmt->bind_result($admin_username);

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
    <title>Admin Dashboard - Gabisan Shipping Lines</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin-dashboard.css">
</head>
<body>
    <button class="hamburger-menu" id="sidebarToggle">
        <span></span>
        <span></span>
        <span></span>
    </button>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">GSL Admin</div>
        <div class="admin-info">
            Welcome, <strong><?php echo htmlspecialchars($admin_username); ?></strong>
        </div>
        <ul class="sidebar-menu">
            <li><a href="javascript:void(0);" onclick="loadDashboard()">
                <i class="fas fa-home"></i> <span>Dashboard</span>
            </a></li>
            <li><a href="javascript:void(0);" onclick="loadContent('schedule_and_rates')">
                <i class="fas fa-ship"></i> <span>Schedule and Rates</span>
            </a></li>
            <li><a href="javascript:void(0);" onclick="loadContent('manage_users')">
                <i class="fas fa-users"></i> <span>Manage Users</span>
            </a></li>
            <li><a href="javascript:void(0);" onclick="loadContent('view_reservation')">
                <i class="fas fa-ticket-alt"></i> <span>View Bookings</span>
            </a></li>
            <li><a href="javascript:void(0);" onclick="loadContent('manage_ferry')">
                <i class="fas fa-ship"></i> <span>Manage Ferry and Schedule</span>
            </a></li>
            <li><a href="javascript:void(0);" onclick="loadContent('reports')">
                <i class="fas fa-chart-bar"></i> <span>Reports</span>
            </a></li>
        </ul>
        <button class="logout-btn" onclick="confirmLogout()">
            <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
        </button>
    </div>

    <div class="main-content" id="mainContent">
        <div class="dashboard-container">
            <div class="dashboard-header">
                <h1>Dashboard Overview</h1>
                <p>Welcome to Gabisan Shipping Lines Inc. Administration Panel</p>
            </div>
            <div class="stats-grid" id="stats-section">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <div class="value">150</div>
                </div>
                <div class="stat-card">
                    <h3>Active Bookings</h3>
                    <div class="value">45</div>
                </div>
                <div class="stat-card">
                    <h3>Total Routes</h3>
                    <div class="value">12</div>
                </div>
                <div class="stat-card">
                    <h3>Today's Revenue</h3>
                    <div class="value">₱2,450</div>
                </div>
            </div>
            <div id="dynamic-content">
                <h2>Welcome to,</h2>
                <div class="content-text">Gabisan Shipping Lines</div>
            </div>
        </div>
    </div>

    <script>
        function confirmLogout() {
            if (confirm('Are you sure you want to log out?')) {
                window.location.href = 'admin_login.php';
            }
        }

        function loadContent(page) {
    const dynamicContent = document.getElementById('dynamic-content');
    const statsSection = document.getElementById('stats-section');
    const dashboardHeader = document.querySelector('.dashboard-header'); // For the dashboard header

    // Hide the stats section and dashboard header
    statsSection.classList.add('hidden');
    dashboardHeader.classList.add('hidden'); // Hide the dashboard overview

    // Add PHP extension if missing
    if (!page.endsWith('.php')) {
        page += '.php';
    }

    // Dynamically load the content
    fetch(page)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then(data => {
            dynamicContent.innerHTML = data;
        })
        .catch(error => {
            dynamicContent.innerHTML = '<p>Error loading content. Please try again later.</p>';
            console.error('Fetch Error:', error);
        });

    // Set active link style
    document.querySelectorAll('.sidebar-menu a').forEach(link => {
        link.classList.remove('active');
    });
    document.querySelector(`.sidebar-menu a[onclick="loadContent('${page}')"]`).classList.add('active');
}

function loadDashboard() {
    const dynamicContent = document.getElementById('dynamic-content');
    const statsSection = document.getElementById('stats-section');
    const dashboardHeader = document.querySelector('.dashboard-header'); // For the dashboard header

    // Show the stats section and dashboard header when returning to dashboard
    statsSection.classList.remove('hidden');
    dashboardHeader.classList.remove('hidden'); // Show the dashboard overview

    // Load the dashboard content
    dynamicContent.innerHTML = `
        <h2>Welcome to, </h2>
        <div class="content-text">Gabisan Shipping Lines</div>
    `;
}
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');

            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                sidebarToggle.classList.toggle('active');
                mainContent.classList.toggle('sidebar-active');
            });

            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768 && 
                    !sidebar.contains(event.target) && 
                    !sidebarToggle.contains(event.target) && 
                    sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                    sidebarToggle.classList.remove('active');
                    mainContent.classList.remove('sidebar-active');
                }
            });
        });
    </script>
</body>
</html>

<style>
:root {
    --primary-color: #6c5ce7;
    --secondary-color: #a29bfe;
    --background-light: #ffffff;
    --card-light: #f9f9f9;
    --text-dark: #333333;
    --text-gray: #b2bec3;
    --purple-accent: #8b5cf6;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', sans-serif;
    background-color: var(--background-light);
    color: var(--text-dark);
    display: flex;
    min-height: 100vh;
}

.sidebar {
    width: 280px;
    background: var(--card-light);
    position: fixed;
    height: 100vh;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    border-right: 1px solid rgba(0,0,0,0.1);
    transform: translateX(-100%);
    transition: transform 0.3s ease-in-out;
    z-index: 1000;
}

.sidebar.active {
    transform: translateX(0);
}

.hamburger-menu {
    position: fixed;
    top: 1rem;
    left: 1rem;
    z-index: 1001;
    background: var(--primary-color);
    border: none;
    color: var(--text-light);
    padding: 0.5rem;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    gap: 4px;
    transition: all 0.3s ease;
}

.hamburger-menu {
    position: fixed;
    top: 1rem;
    left: 1rem;
    z-index: 1001;
    background: none; /* Remove background color */
    border: none;
    padding: 0.5rem;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    gap: 4px;
    transition: all 0.3s ease;
}

.hamburger-menu span {
    display: block;
    width: 25px;
    height: 3px;
    background: #000; /* Black color for the lines */
    border-radius: 2px;
    transition: all 0.3s ease;
}

.hamburger-menu.active span:nth-child(1) {
    transform: rotate(45deg) translate(5px, 5px);
}

.hamburger-menu.active span:nth-child(2) {
    opacity: 0;
}

.hamburger-menu.active span:nth-child(3) {
    transform: rotate(-45deg) translate(5px, -5px);
}

.sidebar-brand {
    font-size: 1.5rem;
    font-weight: 700;
    padding: 1rem 0;
    text-align: center;
    border-bottom: 1px solid rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
    color: var(--text-dark);
}

.admin-info {
    background: rgba(0,0,0,0.05);
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
    color: var(--text-dark);
}

.sidebar-menu {
    list-style: none;
    flex-grow: 1;
}

.sidebar-menu li {
    margin-bottom: 0.5rem;
}

.sidebar-menu a {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    color: var(--text-dark);
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.2s ease;
    gap: 0.75rem;
}

.sidebar-menu a:hover,
.sidebar-menu a.active {
    background: rgba(0,0,0,0.05);
    transform: translateX(5px);
}

.logout-btn {
    background: rgba(220,38,38,0.9);
    color: white;
    border: none;
    padding: 0.75rem;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
    font-size: 0.9rem;
}

.logout-btn:hover {
    background: rgb(220,38,38);
    transform: translateY(-2px);
}

.main-content {
    margin-left: 0;
    flex-grow: 1;
    padding: 2rem;
    padding-top: 4rem;
    transition: margin-left 0.3s ease-in-out;
}

.main-content.sidebar-active {
    margin-left: 280px;
}

.dashboard-container {
    max-width: 1200px;
    margin: 0 auto;
}

.dashboard-header {
    margin-bottom: 2rem;
}

.dashboard-header h1 {
    font-size: 1.875rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: var(--text-dark);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--card-light);
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px rgba(0,0,0,0.2);
}

.stat-card h3 {
    font-size: 0.875rem;
    color: var(--text-gray);
    margin-bottom: 0.5rem;
}

.stat-card .value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
}

#dynamic-content {
    background: var(--card-light);
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.content-text {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary-color);
    text-align: center;
    padding: 20px;
    background-color: rgba(155, 135, 245, 0.1);
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.content-text:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px rgba(0,0,0,0.2);
}

.hidden {
    display: none;
}

@media (max-width: 768px) {
    .main-content.sidebar-active {
        margin-left: 0;
    }
    
    .sidebar {
        width: 100%;
        max-width: 280px;
    }
}
</style>