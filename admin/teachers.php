<?php
/**
 * Teacher Management - SIMS
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
    $teacher_id = (int)$_GET['delete'];
    
    // Get user_id first
    $stmt = $db->getConnection()->prepare("SELECT user_id FROM teachers WHERE teacher_id = ?");
    $stmt->bind_param('i', $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $teacher = $result->fetch_assoc();
    $stmt->close();

    if ($teacher) {
        // Delete user (cascades to teacher)
        $user_id = $teacher['user_id'];
        $stmt = $db->getConnection()->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param('i', $user_id);
        
        if ($stmt->execute()) {
            $message = 'Teacher deleted successfully!';
            logActivity($db, $auth, 'DELETE', 'teachers', $teacher_id, 'Teacher deleted');
        } else {
            $error = 'Failed to delete teacher';
        }
        $stmt->close();
    }
}

// Get all teachers
$result = $db->getConnection()->query(
    "SELECT t.*, u.full_name, u.username, u.email FROM teachers t 
     JOIN users u ON t.user_id = u.user_id 
     ORDER BY t.teacher_id DESC"
);
$teachers = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Management - SIMS</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php require_once '../includes/navbar.php'; ?>
    
    <div class="dashboard-container">
        <div class="page-header">
            <h1>Teacher Management</h1>
            <a href="add-teacher.php" class="btn btn-primary">+ Add New Teacher</a>
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
                    <th>Teacher ID</th>
                    <th>Employee #</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Specialization</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teachers as $teacher): ?>
                <tr>
                    <td>#<?php echo $teacher['teacher_id']; ?></td>
                    <td><?php echo htmlspecialchars($teacher['employee_id']); ?></td>
                    <td><?php echo htmlspecialchars($teacher['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                    <td><?php echo htmlspecialchars($teacher['specialization'] ?? '-'); ?></td>
                    <td>
                        <span class="badge badge-<?php echo $teacher['status'] === 'active' ? 'success' : 'danger'; ?>">
                            <?php echo ucfirst($teacher['status']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="view-teacher.php?id=<?php echo $teacher['teacher_id']; ?>" class="btn btn-small">View</a>
                        <a href="edit-teacher.php?id=<?php echo $teacher['teacher_id']; ?>" class="btn btn-small">Edit</a>
                        <a href="?delete=<?php echo $teacher['teacher_id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>
