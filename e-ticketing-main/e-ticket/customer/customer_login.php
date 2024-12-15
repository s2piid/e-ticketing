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
            $error_message = "Invalid username or password.";
        }
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
    <title>Customer Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f0f2f5;
            font-family: 'Arial', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            display: flex;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            width: 90%;
            max-width: 800px;
        }

        .left-side {
            padding: 30px;
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .right-side {
            width: 50%;
            background: linear-gradient(to right, #89f7fe, #66a6ff);
            color: #fff;
            padding: 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .left-side h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .social-login {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .social-login a {
            display: inline-block;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f0f2f5;
            color: #555;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            transition: background 0.3s;
        }

        .social-login a:hover {
            background: #ddd;
        }

        .form-control {
            margin-bottom: 15px;
        }

        .btn-login {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .btn-login:hover {
            background: #0056b3;
        }

        .error-message {
            color: #ff3333;
            text-align: center;
            margin-bottom: 10px;
        }

        .right-side h2 {
            margin-bottom: 20px;
        }

        .right-side p {
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="left-side">
        <h2>Login</h2>
        <form id="loginForm" method="POST">
            <div class="social-login">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
            </div>
            <input type="text" id="username" name="username" class="form-control" placeholder="Username" required>
            <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
            <button type="submit" class="btn btn-login">Login</button>
            <p class="error-message"><?php if (isset($error_message)) echo $error_message; ?></p>
            <p>Don't have an account? <a href="customer_signup.php">Sign Up</a></p>
        </form>
    </div>
    <div class="right-side">
        <h2>Welcome to Gabisan Shipping Line!!</h2>
        <p>Login to access your account and manage your bookings.</p>
    </div>
</div>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
