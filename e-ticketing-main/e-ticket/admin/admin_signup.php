<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');

// Check if the database connection exists
if (!$conn) {
    die("Database connection error: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = trim($_POST['email']);
    $phone_num = trim($_POST['phone_num']);
    $acc_type = 'admin'; // Setting role as admin

    // Basic validation
    if (empty($username) || empty($password) || empty($confirm_password) || empty($email)) {
        $error_message = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT user_id FROM Users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_message = "Username or email already exists.";
        } else {
            // Insert new admin user
            $stmt = $conn->prepare("INSERT INTO users (username, password, acc_type, email, phone_num, created_at) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");
            $stmt->bind_param("sssss", $username, $password_hash, $acc_type, $email, $phone_num);

            if ($stmt->execute()) {
                $_SESSION['admin_id'] = $stmt->insert_id;
                header("Location: admin_dashboard.php");
                exit();
            } else {
                $error_message = "Error creating account. Please try again.";
            }
        }

        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Signup</title>
<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
    }

    body {
        background: linear-gradient(to right, #007bff, #00c6ff);
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
    }

    .login-container {
        background: #fff;
        width: 400px;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        text-align: center;
    }

    h2 {
        margin-bottom: 20px;
        color: #333;
    }

    .input-group {
        margin-bottom: 15px;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="phone_num"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
    }

    input:focus {
        border-color: #007bff;
        outline: none;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }

    .btn-login {
        width: 100%;
        padding: 10px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-login:hover {
        background-color: #0056b3;
    }

    .error-message {
        color: #ff3333;
        font-size: 14px;
        margin-bottom: 10px;
    }

    a {
        color: #007bff;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }

    p {
        margin-top: 15px;
        font-size: 14px;
        color: #333;
    }
</style>
</head>
<body>
<div class="login-container">
    <form id="signupForm" action="" method="POST">
        <h2>Admin Signup</h2>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <div class="input-group">
            <input type="text" id="username" name="username" placeholder="Username" required>
        </div>
        <div class="input-group">
            <input type="email" id="email" name="email" placeholder="Email" required>
        </div>
        <div class="input-group">
            <input type="phone_num" id="phone_num" name="phone_num" placeholder="Phone" required>
        </div>
        <div class="input-group">
            <input type="password" id="password" name="password" placeholder="Password" required>
        </div>
        <div class="input-group">
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
        </div>
        <button type="submit" class="btn-login">Sign Up</button>
        <p>Already have an account? <a href="admin_login.php">Login here</a></p>
    </form>
</div>
</body>
</html>
