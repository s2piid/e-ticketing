<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');

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
        // Verify the password using password_verify
        if (password_verify($password, $password_hash)) {
            $_SESSION['admin_id'] = $user_id;
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "Invalid username or role.";
    }

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
<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
    }

    body {
        background: linear-gradient(to right, #6a11cb, #2575fc);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        font-family: Arial, sans-serif;
    }

    .login-container {
        background: #fff;
        padding: 30px;
        width: 350px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        border-radius: 10px;
        text-align: center;
    }

    h2 {
        margin-bottom: 20px;
        color: #333;
    }

    .input-group {
        margin-bottom: 15px;
        position: relative;
    }

    input[type="text"],
    input[type="password"] {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
    }

    input:focus {
        border-color: #6a11cb;
        outline: none;
        box-shadow: 0 0 5px rgba(106, 17, 203, 0.5);
    }

    .btn-login {
        width: 100%;
        padding: 12px;
        background: #6a11cb;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s;
    }

    .btn-login:hover {
        background: #4e0fba;
    }

    .error-message {
        color: #ff4d4d;
        font-size: 14px;
        margin-bottom: 15px;
    }

    .signup-link {
        margin-top: 15px;
        font-size: 14px;
    }

    .signup-link a {
        color: #6a11cb;
        text-decoration: none;
    }

    .signup-link a:hover {
        text-decoration: underline;
    }
</style>
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
        <div class="signup-link">
            <p>Don’t have an account? <a href="admin_signup.php">Sign Up</a></p>
        </div>
    </form>
</div>
</body>
</html>
