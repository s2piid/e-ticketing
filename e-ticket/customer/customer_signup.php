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
<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
}

body {
    background-color: #f0f2f5;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.login-container {
    background: #fff;
    padding: 30px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
    border-radius: 8px;
    width: 300px;
}

h2 {
    text-align: center;
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
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

input:focus {
    border-color: #007bff;
}

.btn-login {
    width: 100%;
    padding: 10px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-login:hover {
    background-color: #0056b3;
}

.error-message {
    color: #ff3333;
    font-size: 14px;
    text-align: center;
    margin-bottom: 10px;
}
body {
    background: #f0f2f5;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    font-family: Arial, sans-serif;
}

.login-container {
    background: #fff;
    padding: 30px;
    width: 300px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #007bff;
}

input {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

button {
    width: 100%;
    padding: 10px;
    background: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

button:hover {
    background: #0056b3;
}

.error-message {
    color: #ff3333;
    text-align: center;
    margin-bottom: 10px;
}


</style>
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
