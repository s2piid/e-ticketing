<?php
// Starting the session
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $error_message = "";

    if (!$conn) {
        die("Database connection error: " . mysqli_connect_error());
    }

    // Prepare SQL query to fetch user details
    $stmt = $conn->prepare("SELECT user_id, password, acc_type FROM Users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $user_id = $row['user_id'];
        $password_hash = $row['password'];
        $account_type = $row['acc_type'];

        // Verify the password using password_verify
        if (password_verify($password, $password_hash)) {
            // Password is correct, start the session
            $_SESSION['customer_id'] = $user_id;
            $_SESSION['haslogin'] = true;
            $_SESSION['acc_type'] = $account_type; // Store account type

            // Redirect based on user type
            if ($row['acc_type'] === 'customer') {
                $_SESSION['customer_id'] = $row['user_id'];
                $_SESSION['acc_type'] = $row['acc_type']; // Store the user account type
                header("Location: customer_dashboard.php");
                exit();
            } else {
                $error_message = "Invalid user type.";
            }
            
    } else {
        $error_message = "Invalid username or user not found.";
    }

    // Close the statement and connection
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
<title>Customer Login</title>
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
    <form id="loginForm" method="POST">
        <h2>Customer Login</h2>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <input type="text" id="username" name="username" placeholder="Username" required>
        <input type="password" id="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
        <p>Don't have an account? 
            <a href="customer_signup.php">Sign Up</a></p>
    </form>
</div>
</body>
</html>
