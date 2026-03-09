<?php
/**
 * Student Management - SIMS
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
    $student_id = (int)$_GET['delete'];
    
    // Get user_id first
    $stmt = $db->getConnection()->prepare("SELECT user_id FROM students WHERE student_id = ?");
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();

    if ($student) {
        // Delete user (this will cascade delete student)
        $user_id = $student['user_id'];
        $stmt = $db->getConnection()->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param('i', $user_id);
        
        if ($stmt->execute()) {
            $message = 'Student deleted successfully!';
            logActivity($db, $auth, 'DELETE', 'students', $student_id, 'Student deleted');
        } else {
            $error = 'Failed to delete student';
        }
        $stmt->close();
    }
}

// Get all students
$result = $db->getConnection()->query(
    "SELECT s.*, u.full_name, u.username, u.email FROM students s 
     JOIN users u ON s.user_id = u.user_id 
     ORDER BY s.student_id DESC"
);
$students = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management - SIMS</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php require_once '../includes/navbar.php'; ?>
    
    <div class="dashboard-container">
        <div class="page-header">
            <h1>Student Management</h1>
            <a href="add-student.php" class="btn btn-primary">+ Add New Student</a>
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
                    <th>Student ID</th>
                    <th>Registration #</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Class Level</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td>#<?php echo $student['student_id']; ?></td>
                    <td><?php echo htmlspecialchars($student['registration_number']); ?></td>
                    <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                    <td><?php echo htmlspecialchars($student['class_level'] ?? '-'); ?></td>
                    <td>
                        <span class="badge badge-<?php echo $student['status'] === 'active' ? 'success' : 'danger'; ?>">
                            <?php echo ucfirst($student['status']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="view-student.php?id=<?php echo $student['student_id']; ?>" class="btn btn-small">View</a>
                        <a href="edit-student.php?id=<?php echo $student['student_id']; ?>" class="btn btn-small">Edit</a>
                        <a href="?delete=<?php echo $student['student_id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>
