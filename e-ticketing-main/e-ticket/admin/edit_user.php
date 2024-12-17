<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');
include 'header.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if the user ID is passed in the URL
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Fetch the user data to pre-fill the form
    $query = "SELECT * FROM users WHERE user_id = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        // If user data does not exist, redirect to manage users
        if (!$user) {
            header("Location: manage_users.php?error=User not found.");
            exit();
        }
    }
} else {
    // If no user ID is set, redirect to manage users
    header("Location: manage_users.php?error=No user ID specified.");
    exit();
}

// Handle form submission for editing user data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone_num = $_POST['phone_num'];

    // Validate and update the user data in the database
    $update_query = "UPDATE users SET username = ?, email = ?, phone_num = ? WHERE user_id = ?";
    if ($stmt = $conn->prepare($update_query)) {
        $stmt->bind_param('sssi', $username, $email, $phone_num, $user_id);
        if ($stmt->execute()) {
            header("Location: manage_users.php?success=User details updated successfully.");
            exit();
        } else {
            $error_message = "Error updating user details.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom Styling */
        body {
            background-color: #f4f7fc;
            font-family: 'Arial', sans-serif;
        }

        .container {
            max-width: 700px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .form-group label {
            font-weight: bold;
            color: #333;
        }

        .form-control {
            border-radius: 8px;
            box-shadow: none;
            border: 1px solid #ddd;
        }

        .btn-primary {
            background-color: #007BFF;
            border-color: #007BFF;
            border-radius: 8px;
            padding: 10px 20px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .back-button a {
            text-decoration: none;
            font-size: 18px;
            color: #007BFF;
            padding: 12px 20px;
            border-radius: 8px;
            transition: background-color 0.3s;
            display: inline-block;
            margin-top: 30px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
        }

        .back-button a:hover {
            background-color: #e2e6ea;
        }

        .alert {
            border-radius: 8px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">Edit User</h2>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <form method="POST" action="edit_user.php?user_id=<?php echo $user_id; ?>">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="phone_num">Phone Number</label>
            <input type="text" class="form-control" id="phone_num" name="phone_num" value="<?php echo htmlspecialchars($user['phone_num']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Save Changes</button>
    </form>

    <div class="back-button">
        <a href="manage_users.php">Back</a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php include 'footer.php'; ?>
