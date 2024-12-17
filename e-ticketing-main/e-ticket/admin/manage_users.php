<?php
session_start();
include('C:/xampp/htdocs/e-ticketing-main/e-ticket/config.php');
include 'header.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle delete action securely using GET parameter
if (isset($_GET['delete_id'])) {
    $user_id = $_GET['delete_id'];
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
<meta charset='UTF-8'>
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<title>Manage Users</title>
<link href='https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css' rel='stylesheet'>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
        margin: 0;
        padding: 0;
    }
    .container {
        max-width: 1200px;
        margin: 50px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .header {
        text-align: center;
        margin: 20px 0;
    }
    .back-button a {
        text-decoration: none;
        font-size: 18px;
        color: white;
        background-color: #007BFF;
        padding: 12px 20px;
        border-radius: 8px;
        transition: background-color 0.3s;
        display: inline-block;
        margin-top: 20px;
        text-align: center;
    }
    .back-button a:hover {
        background-color: #0056b3;
    }
    table {
        width: 100%;
        margin-top: 20px;
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: center;
    }
    th {
        background-color: #007BFF;
        color: white;
    }
    tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    tr:hover {
        background-color: #ddd;
    }
    .action-buttons a {
        text-decoration: none;
        color: white;
        padding: 8px 15px;
        border-radius: 5px;
        margin: 0 5px;
    }
    .edit-btn {
        background-color: #28a745;
    }
    .edit-btn:hover {
        background-color: #218838;
    }
    .delete-btn {
        background-color: #dc3545;
    }
    .delete-btn:hover {
        background-color: #c82333;
    }
    .pagination a {
        padding: 8px 15px;
        margin: 0 8px;
        border: 1px solid #ddd;
        color: #007BFF;
        text-decoration: none;
        border-radius: 5px;
    }
    .pagination a:hover {
        background-color: #f2f2f2;
    }
</style>
</head>
<body>

<div class="container">
    <h2 class="header">Manage Users</h2>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
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
                    <td class="action-buttons">
                        <a href="edit_user.php?user_id=<?php echo $row['user_id']; ?>" class="edit-btn">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="#" class="delete-btn" data-toggle="modal" data-target="#deleteModal" data-id="<?php echo $row['user_id']; ?>">
                            <i class="fas fa-trash-alt"></i> Delete
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="back-button">
        <a href="admin_dashboard.php">Back</a>
    </div>

</div>

<!-- Modal for confirmation -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Delete User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this user? This action cannot be undone.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <a href="#" id="confirmDelete" class="btn btn-danger">Delete</a>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    // Capture the data-id attribute and set it for the confirmation link
    $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); 
        var userId = button.data('id'); 
        var modal = $(this);
        modal.find('#confirmDelete').attr('href', 'delete_user.php?user_id=' + userId);
    });
</script>

</body>
</html>

<?php include 'footer.php'; ?>
