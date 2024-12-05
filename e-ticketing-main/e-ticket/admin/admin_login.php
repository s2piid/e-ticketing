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
<script src="script.js"></script>
</head>
<body>
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
        <div>
            <button type="submit" class="btn-login">Login</button>
        </div>
        <br>
        <div>
            <button type ="submit" href="admin_signup.php">Sign Up</a>
        </div>
        
    </form>
</div>
</body>
</html>
