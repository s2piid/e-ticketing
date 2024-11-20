<?php
session_start();
include('C:/xampp/htdocs/e-ticket/config.php'); // Ensure the correct path

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle user actions (edit/delete)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $user_id = $_POST['user_id'];
    $action = $_POST['action'];

    if ($action == 'edit') {
        // Redirect to the user edit page
        header("Location: edit_user.php?user_id=" . $user_id);
        exit();
    } elseif ($action == 'delete') {
        // Handle delete action (permanent delete or soft delete)
        $delete_query = "UPDATE users SET deleted_at = NOW() WHERE user_id = ?";
        if ($stmt = $conn->prepare($delete_query)) {
            $stmt->bind_param('i', $user_id);
            if ($stmt->execute()) {
                $success_message = "User has been deleted.";
            } else {
                $error_message = "Error deleting user.";
            }
            $stmt->close();
        }
    }
}

// Fetch all users (customers)
$query = "
    SELECT 
        user_id,
        username,
        acc_type,
        email,
        phone_num,
        created_at,
        updated_at,
        deleted_at
    FROM 
        users
    WHERE
        acc_type = 'customer'
    ORDER BY 
        user_id ASC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="C:/xampp/htdocs/e-ticket/style.css">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Root Variables */
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --light-color: #f0f2f5;
            --background-color: #fff;
            --text-dark: #333;
            --hover-bg: #f8f9fa;
            --border-color: #dee2e6;
        }

        /* General Body Styling */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-color);
            margin: 0;
            padding: 0;
            color: var(--text-dark);
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }

        /* Container Styling */
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 30px;
            background-color: var(--background-color);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            flex-grow: 1;
        }

        /* Header Styling */
        h2 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 30px;
            font-size: 28px;
            letter-spacing: 1px;
        }

        /* Success and Error Messages */
        .success-message {
            color: #155724;
            background-color: #d4edda;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #c3e6cb;
            text-align: center;
            margin-bottom: 20px;
        }

        .error-message {
            color: #721c24;
            background-color: #f8d7da;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #f5c6cb;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            border-radius: 8px;
            overflow: hidden;
        }

        table th, table td {
            padding: 15px;
            border: 1px solid var(--border-color);
            text-align: left;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        table th {
            background-color: #e9ecef;
            color: var(--text-dark);
            font-weight: bold;
        }

        table tbody tr:nth-child(even) {
            background-color: var(--hover-bg);
        }

        table tbody tr:hover {
            background-color: var(--secondary-color);
            color: #fff;
        }

        /* Icon Styling */
        .btn-action i {
            font-size: 18px;
        }

        .btn-action {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.2s ease;
        }

        .btn-edit {
            background-color: #ffc107;
            color: #fff;
        }

        .btn-delete {
            background-color: #dc3545;
            color: #fff;
        }

        .btn-edit:hover {
            background-color: #e0a800;
            transform: translateY(-2px);
        }

        .btn-delete:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                width: 100%;
                padding: 15px;
            }

            h2 {
                font-size: 24px;
            }

            table th, table td {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Users</h2>

        <?php if (isset($success_message)): ?>
            <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Deleted At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone_num']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td><?php echo htmlspecialchars($row['updated_at']); ?></td>
                        <td><?php echo htmlspecialchars($row['deleted_at']); ?></td>
                        <td>
                            <form method="post" style="display:inline-block;">
                                <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                <button type="submit" name="action" value="edit" class="btn-action btn-edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="submit" name="action" value="delete" class="btn-action btn-delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
