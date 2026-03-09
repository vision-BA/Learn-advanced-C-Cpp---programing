<?php
/**
 * Enrollment Management - SIMS
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
    $enrollment_id = (int)$_GET['delete'];
    
    $stmt = $db->getConnection()->prepare("DELETE FROM enrollments WHERE enrollment_id = ?");
    $stmt->bind_param('i', $enrollment_id);
    
    if ($stmt->execute()) {
        $message = 'Enrollment deleted successfully!';
        logActivity($db, $auth, 'DELETE', 'enrollments', $enrollment_id, 'Enrollment deleted');
    } else {
        $error = 'Failed to delete enrollment';
    }
    $stmt->close();
}

// Get all enrollments
$result = $db->getConnection()->query(
    "SELECT e.*, s.registration_number, u.full_name as student_name, c.course_code, c.course_name 
     FROM enrollments e 
     JOIN students s ON e.student_id = s.student_id 
     JOIN users u ON s.user_id = u.user_id 
     JOIN courses c ON e.course_id = c.course_id 
     ORDER BY e.enrollment_id DESC"
);
$enrollments = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Management - SIMS</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php require_once '../includes/navbar.php'; ?>
    
    <div class="dashboard-container">
        <div class="page-header">
            <h1>Enrollment Management</h1>
            <a href="add-enrollment.php" class="btn btn-primary">+ Add New Enrollment</a>
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
                    <th>Enrollment ID</th>
                    <th>Student</th>
                    <th>Reg. #</th>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Enrollment Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($enrollments as $enrollment): ?>
                <tr>
                    <td>#<?php echo $enrollment['enrollment_id']; ?></td>
                    <td><?php echo htmlspecialchars($enrollment['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($enrollment['registration_number']); ?></td>
                    <td><?php echo htmlspecialchars($enrollment['course_code']); ?></td>
                    <td><?php echo htmlspecialchars($enrollment['course_name']); ?></td>
                    <td><?php echo formatDate($enrollment['enrollment_date']); ?></td>
                    <td>
                        <span class="badge badge-<?php echo $enrollment['status'] === 'active' ? 'success' : ($enrollment['status'] === 'completed' ? 'info' : 'danger'); ?>">
                            <?php echo ucfirst($enrollment['status']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="edit-enrollment.php?id=<?php echo $enrollment['enrollment_id']; ?>" class="btn btn-small">Edit</a>
                        <a href="?delete=<?php echo $enrollment['enrollment_id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>
