<?php
session_start();

// Ensure the user is logged in before accessing the dashboard
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

// Include your database connection
include ('C:/xampp/htdocs/e-ticket/config.php');

// Fetch user details from the database
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
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
}

.dashboard-container {
    max-width: 1200px;
    margin: 0 auto;
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
}

header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #4CAF50;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.logo img {
    height: 50px;
    width: 200px;
}

nav ul {
    list-style-type: none;
    display: flex;
    gap: 20px;
}

nav ul li a {
    color: #fff;
    text-decoration: none;
    font-weight: bold;
}

h2 {
    font-size: 2em;
    color: #333;
    margin-bottom: 20px;
}

.dashboard-content {
    text-align: center;
}

.dashboard-buttons {
    display: flex;
    gap: 20px;
    justify-content: center;
    margin-top: 20px;
}

.dashboard-buttons button {
    padding: 10px 20px;
    font-size: 1em;
    cursor: pointer;
    border: none;
    border-radius: 5px;
    background-color: #4CAF50;
    color: #fff;
    transition: background-color 0.3s;
}

.dashboard-buttons button:hover {
    background-color: #45a049;
}

footer {
    margin-top: 30px;
    text-align: center;
    font-size: 0.9em;
    color: #777;
}
</style>
    <script src="script.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <header>
            <div class="logo">
                <img src="gabisan2.jpg" alt="Logo" class="logo-img"> <!-- Add your logo here -->
            </div>
            <nav>
                <ul>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="services.php">Services</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <li><a href="news.php">News & Updates</a></li>
                    <li><a href="signout.php">Sign Out</a></li>
                </ul>
            </nav>
        </header>

        <div class="dashboard-content">
            <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
            <p>Email: <?php echo htmlspecialchars($email); ?></p>
            <div class="dashboard-buttons">
                <button onclick="window.location.href='schedule_and_rate.php'">Schedule and Rates</button>
                <button onclick="window.location.href='book.php'">Book</button>
                <button onclick="window.location.href='book_history.php'">Booking History</button>
                <button onclick="window.location.href='update_pro.php'">Update Profile</button>
                <button onclick="window.location.href='change_pass.php'">Change Password</button>
            </div>
        </div>
    </div>
</body>
</html>
