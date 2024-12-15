<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: 'Arial', sans-serif;
        }

        .signup-container {
            background: #ffffff;
            padding: 30px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .signup-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #007bff;
        }

        .form-control {
            margin-bottom: 15px;
        }

        .btn-signup {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .btn-signup:hover {
            background: #0056b3;
        }

        .error-message {
            color: #ff3333;
            text-align: center;
            margin-bottom: 10px;
        }

        .signup-container a {
            color: #007bff;
            text-decoration: none;
        }

        .signup-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="signup-container">
    <form id="signupForm" method="POST">
        <h2>Customer Signup</h2>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <div class="mb-3">
            <input type="text" id="username" name="username" class="form-control" placeholder="Username" required>
        </div>
        <div class="mb-3">
            <input type="email" id="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="mb-3">
            <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="mb-3">
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
        </div>
        <div class="mb-3">
            <input type="text" id="phone_num" name="phone_num" class="form-control" placeholder="Phone Number" required>
        </div>
        <button type="submit" class="btn btn-signup">Sign Up</button>
        <p class="mt-3 text-center">Already have an account? <a href="customer_login.php">Login</a></p>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
