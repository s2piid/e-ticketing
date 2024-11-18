<?php
session_start();
include ('C:/xampp/htdocs/e-ticket/config.php'); // Ensure the correct path to your config.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $error_message = "";

    // Check if the database connection exists
    if (!$conn) {
        die("Database connection error: " . mysqli_connect_error());
    }

    // Query to check admin credentials
    $stmt = $conn->prepare("SELECT user_id, password FROM Users WHERE username = ? AND acc_type = 'admin'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $password_hash);
        $stmt->fetch();
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        // Verify the password using password_verify
        if (password_verify($password, $password_hash)) {
            // Password is correct, start admin session
            $_SESSION['admin_id'] = $user_id;
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "Invalid username or role.";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login</title>
<link rel="stylesheet" href="style.css">
<script src="script.js"></script>
</head>
<body>
<div class="login-container">
    <form id="loginForm" action="" method="POST">
        <h2>Admin Login</h2>
        <?php if (isset($error_message) && !empty($error_message)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <div class="input-group">
            <input type="text" id="username" name="username" placeholder="Username" required>
        </div>
        <div class="input-group">
            <input type="password" id="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit" class="btn-login">Login</button>
        <a href="admin_signup.php">Sign Up</a>
    </form>
</div>
</body>
</html>
