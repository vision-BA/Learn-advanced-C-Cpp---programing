<?php
/**
 * User Management - SIMS
 */

require_once '../config.php';
require_once '../database.php';
require_once '../auth.php';
require_once '../functions.php';

// Check permission
checkPermission(ROLE_ADMIN, $auth);

$message = '';
$error = '';

// Handle delete
if (isset($_GET['delete'])) {
    $user_id = (int)$_GET['delete'];
    
    // Don't allow deleting yourself
    $user = $auth->getCurrentUser();
    if ($user_id === $user['user_id']) {
        $error = 'Cannot delete your own account!';
    } else {
        $stmt = $db->getConnection()->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param('i', $user_id);
        
        if ($stmt->execute()) {
            $message = 'User deleted successfully!';
            logActivity($db, $auth, 'DELETE', 'users', $user_id, 'User deleted');
        } else {
            $error = 'Failed to delete user';
        }
        $stmt->close();
    }
}

// Get all users
$result = $db->getConnection()->query(
    "SELECT user_id, username, email, full_name, role, status, created_at FROM users ORDER BY user_id DESC"
);
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - SIMS</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php require_once '../includes/navbar.php'; ?>
    
    <div class="dashboard-container">
        <div class="page-header">
            <h1>User Management</h1>
            <a href="add-user.php" class="btn btn-primary">+ Add New User</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <table class="data-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td>#<?php echo $user['user_id']; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <span class="badge badge-<?php echo $user['role'] === ROLE_ADMIN ? 'danger' : ($user['role'] === ROLE_TEACHER ? 'warning' : 'info'); ?>">
                            <?php echo ucfirst($user['role']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-<?php echo $user['status'] === STATUS_ACTIVE ? 'success' : 'danger'; ?>">
                            <?php echo ucfirst($user['status']); ?>
                        </span>
                    </td>
                    <td><?php echo formatDateTime($user['created_at']); ?></td>
                    <td>
                        <a href="edit-user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-small">Edit</a>
                        <a href="?delete=<?php echo $user['user_id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>
