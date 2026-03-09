<?php
/**
 * Course Management - SIMS
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
    $course_id = (int)$_GET['delete'];
    
    $stmt = $db->getConnection()->prepare("DELETE FROM courses WHERE course_id = ?");
    $stmt->bind_param('i', $course_id);
    
    if ($stmt->execute()) {
        $message = 'Course deleted successfully!';
        logActivity($db, $auth, 'DELETE', 'courses', $course_id, 'Course deleted');
    } else {
        $error = 'Failed to delete course';
    }
    $stmt->close();
}

// Get all courses
$result = $db->getConnection()->query(
    "SELECT c.*, u.full_name as teacher_name FROM courses c 
     LEFT JOIN teachers t ON c.teacher_id = t.teacher_id 
     LEFT JOIN users u ON t.user_id = u.user_id 
     ORDER BY c.course_id DESC"
);
$courses = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Management - SIMS</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php require_once '../includes/navbar.php'; ?>
    
    <div class="dashboard-container">
        <div class="page-header">
            <h1>Course Management</h1>
            <a href="add-course.php" class="btn btn-primary">+ Add New Course</a>
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
                    <th>Course ID</th>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Teacher</th>
                    <th>Class Level</th>
                    <th>Credits</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $course): ?>
                <tr>
                    <td>#<?php echo $course['course_id']; ?></td>
                    <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                    <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                    <td><?php echo htmlspecialchars($course['teacher_name'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($course['class_level'] ?? '-'); ?></td>
                    <td><?php echo $course['credits']; ?></td>
                    <td>
                        <span class="badge badge-<?php echo $course['status'] === 'active' ? 'success' : 'danger'; ?>">
                            <?php echo ucfirst($course['status']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="edit-course.php?id=<?php echo $course['course_id']; ?>" class="btn btn-small">Edit</a>
                        <a href="?delete=<?php echo $course['course_id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>
