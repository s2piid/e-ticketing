<?php
session_start();
include('C:/xampp/htdocs/e-ticket/config.php');
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
$admin_id = $_SESSION['admin_id'];
$stmt = $conn->prepare("SELECT username FROM Users WHERE user_id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$stmt->bind_result($admin_username);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Gabisan Shipping Lines</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --accent-color: #3b82f6;
            --background-light: #f8fafc;
            --text-dark: #1e293b;
            --text-light: #f8fafc;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.12);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-light);
            color: var(--text-dark);
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 280px;
            background: linear-gradient(to bottom, var(--primary-color), var(--secondary-color));
            color: var(--text-light);
            position: fixed;
            height: 100vh;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }
        .sidebar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            padding: 1rem 0;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 1.5rem;
        }
        .admin-info {
            background: rgba(255,255,255,0.1);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
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
            color: var(--text-light);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s ease;
            gap: 0.75rem;
        }
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        .sidebar-menu i {
            width: 20px;
            text-align: center;
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
            margin-left: 280px;
            flex-grow: 1;
            padding: 2rem;
            transition: all 0.3s ease;
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
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: var(--shadow-md);
            transition: all 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }
        .stat-card h3 {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 0.5rem;
        }
        .stat-card .value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        #dynamic-content {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: var(--shadow-md);
        }
        .content-text {
            font-family: 'Poppins', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            text-align: center;
            padding: 20px;
            background-color: rgba(37, 99, 235, 0.1);
            border-radius: 12px;
            box-shadow: var(--shadow-md);
            transition: transform 0.3s ease;
        }

        .content-text:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .hidden {
            display: none;
        }
        
        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
                padding: 1rem 0.5rem;
            }
            .sidebar-brand {
                font-size: 1.2rem;
                padding: 0.5rem;
            }
            .sidebar-menu a span,
            .admin-info {
                display: none;
            }
            .main-content {
                margin-left: 80px;
            }
            .logout-btn span {
                display: none;
            }
        }
        @media (max-width: 640px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .main-content {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-brand">GSL Admin</div>
        <div class="admin-info">
            Welcome, <strong><?php echo htmlspecialchars($admin_username); ?></strong>
        </div>
        <ul class="sidebar-menu">
            <li><a href="javascript:void(0);" onclick="loadContent('manage_users')">
                <i class="fas fa-users"></i> <span>Manage Users</span>
            </a></li>
            <li><a href="javascript:void(0);" onclick="loadContent('view_reservation')">
                <i class="fas fa-ticket-alt"></i> <span>View Bookings</span>
            </a></li>
            <li><a href="javascript:void(0);" onclick="loadContent('manage_ferry')">
                <i class="fas fa-ship"></i> <span>Manage Ferry</span>
            </a></li>
            <li><a href="javascript:void(0);" onclick="loadContent('reports')">
                <i class="fas fa-chart-bar"></i> <span>Reports</span>
            </a></li>
        </ul>
        <button class="logout-btn" onclick="confirmLogout()">
            <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
        </button>
    </div>
    <div class="main-content">
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
                <h2>Welcome to, </h2>
                <div class="content-text">Gabisan Shipping Lines</div>
            </div>
        </div>
    </div>
    <script>
        function confirmLogout() {
            if (confirm('Are you sure you want to log out?')) {
                window.location.href = 'admin_logout.php';
            }
        }

        function loadContent(page) {
            const dynamicContent = document.getElementById('dynamic-content');
            const statsSection = document.getElementById('stats-section');
            
            // Hide the stats section
            statsSection.classList.add('hidden');

            // Dynamically load the content
            fetch(page + '.php')
                .then(response => response.text())
                .then(data => {
                    dynamicContent.innerHTML = data;
                })
                .catch(error => {
                    dynamicContent.innerHTML = 'Error loading content. Please try again later.';
                    console.error(error);
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
            
            // Show the stats section again when returning to dashboard
            statsSection.classList.remove('hidden');

            // Load the dashboard content
            dynamicContent.innerHTML = `
                <h2>Welcome to, </h2>
                <div class="content-text">Gabisan Shipping Lines</div>
            `;
        }
    </script>
</body>
</html>
