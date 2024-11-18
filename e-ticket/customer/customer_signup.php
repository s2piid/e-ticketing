<?php
session_start();
include ('C:/xampp/htdocs/e-ticket/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = trim($_POST['email']);
    $phone_num = trim($_POST['phone_num']);
    $acc_type = 'customer'; // Setting role as customer

    if (empty($username) || empty($password) || empty($confirm_password) || empty($email)) {
        $error_message = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $error_message = "Password must be at least 8 characters long, include at least one letter, one number, and one special character.";
    }
     else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT user_id FROM Users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_message = "Username or email already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO Users (username, password, acc_type, email, phone_num, created_at) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");
            $stmt->bind_param("sssss", $username, $password_hash, $acc_type, $email, $phone_num);

            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id;
                header("Location: customer_login.php");
                exit();
            } else {
                $error_message = "Error creating account.";
            }
        }

        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Signup</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="login-container">
    <form id="signupForm" method="POST">
        <h2>Customer Signup</h2>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <input type="text" id="username" name="username" placeholder="Username" required>
        <input type="email" id="email" name="email" placeholder="Email" required>
        <input type="password" id="password" name="password" placeholder="Password" required>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
        <input type="text" id="phone_num" name="phone_num" placeholder="Phone Number" required>
        <button type="submit">Sign Up</button>
        <p>Already have an account? <a href="customer_login.php">Login</a></p>
    </form>
</div>
</body>
</html>
