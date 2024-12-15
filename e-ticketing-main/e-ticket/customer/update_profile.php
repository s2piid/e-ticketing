<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');

// Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

$user_id = $_SESSION['customer_id']; // Assuming the user ID is stored in the session

// Fetch user details
$query = $conn->prepare("SELECT username, email, phone_num FROM users WHERE user_id = ? AND deleted_at IS NULL");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    die("Error: User not found.");
}

$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone_num = $_POST['phone_num'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    // Update query
    if ($password) {
        $update_query = $conn->prepare("
            UPDATE users 
            SET username = ?, email = ?, phone_num = ?, password = ?, updated_at = NOW() 
            WHERE user_id = ? AND deleted_at IS NULL
        ");
        $update_query->bind_param("ssssi", $username, $email, $phone_num, $password, $user_id);
    } else {
        $update_query = $conn->prepare("
            UPDATE users 
            SET username = ?, email = ?, phone_num = ?, updated_at = NOW() 
            WHERE user_id = ? AND deleted_at IS NULL
        ");
        $update_query->bind_param("sssi", $username, $email, $phone_num, $user_id);
    }

    if ($update_query->execute()) {
        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: update_profile.php");
        exit();
    } else {
        $error_message = "Error updating profile: " . $update_query->error;
    }
}

$pageTitle = "Update Profile";
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
        }
        .card {
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .alert {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert svg {
            flex-shrink: 0;
        }
        .password-toggle {
            position: relative;
        }
        .password-toggle .toggle-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }
    </style>
</head>
<body>

<section class="container mt-5">
    <div class="card p-4">
        <h2 class="mb-4 text-center">Update Profile</h2>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8a8 8 0 1 1-16 0 8 8 0 0 1 16 0zM4.646 4.646a.5.5 0 0 0-.708.708L7.293 8 3.939 11.354a.5.5 0 1 0 .708.708L8 8.707l3.354 3.355a.5.5 0 0 0 .708-.708L8.707 8l3.355-3.354a.5.5 0 0 0-.708-.708L8 7.293 4.646 4.646z"/>
                </svg>
                <?= $error_message ?>
            </div>
        <?php elseif (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM6.134 12.134l6.518-6.518a.5.5 0 1 0-.707-.707l-5.812 5.812L3.854 8.854a.5.5 0 1 0-.708.708l2.988 2.989z"/>
                </svg>
                <?= $_SESSION['success'] ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form method="POST" action="update_profile.php">
            <div class="mb-3">
                <label for="username" class="form-label">Username <small class="text-muted">(Required)</small></label>
                <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email <small class="text-muted">(Required)</small></label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone_num" class="form-label">Phone Number <small class="text-muted">(Required)</small></label>
                <input type="text" class="form-control" id="phone_num" name="phone_num" value="<?= htmlspecialchars($user['phone_num']) ?>" required>
            </div>
            <div class="mb-3 password-toggle">
                <label for="password" class="form-label">New Password <small class="text-muted">(Leave blank to keep current password)</small></label>
                <input type="password" class="form-control" id="password" name="password">
                <span class="toggle-icon" onclick="togglePasswordVisibility()">👁️</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <a href="customer_dashboard.php" class="btn btn-secondary">Back</a>
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </div>
        </form>
    </div>
</section>

<script>
    function togglePasswordVisibility() {
        const passwordField = document.getElementById('password');
        const type = passwordField.type === 'password' ? 'text' : 'password';
        passwordField.type = type;
    }
</script>

</body>
</html>

<?php include 'footer.php'; ?>
