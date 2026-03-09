<?php
/**
 * Student Courses - SIMS
 */

require_once '../config.php';
require_once '../database.php';
require_once '../auth.php';
require_once '../functions.php';

// Check permission
checkPermission(ROLE_STUDENT, $auth);

$user = $auth->getCurrentUser();

// Get student ID
$stmt = $db->getConnection()->prepare("SELECT student_id FROM students WHERE user_id = ?");
$stmt->bind_param('i', $user['user_id']);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();
$student_id = $student['student_id'];

// Get all enrolled courses
$result = $db->getConnection()->query(
    "SELECT e.*, c.course_code, c.course_name, c.description, c.credits, u.full_name as teacher_name 
     FROM enrollments e 
     JOIN courses c ON e.course_id = c.course_id 
     LEFT JOIN teachers t ON c.teacher_id = t.teacher_id 
     LEFT JOIN users u ON t.user_id = u.user_id 
     WHERE e.student_id = $student_id 
     ORDER BY c.course_name"
);
$courses = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses - SIMS</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php require_once '../includes/navbar.php'; ?>
    
    <div class="dashboard-container">
        <h1>My Courses</h1>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Instructor</th>
                    <th>Credits</th>
                    <th>Enrollment Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($courses) > 0): ?>
                    <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($course['course_name']); ?></strong>
                            <br><small><?php echo htmlspecialchars($course['description'] ?? ''); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($course['teacher_name'] ?? '-'); ?></td>
                        <td><?php echo $course['credits']; ?></td>
                        <td><?php echo formatDate($course['enrollment_date']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $course['status'] === 'active' ? 'success' : ($course['status'] === 'completed' ? 'info' : 'danger'); ?>">
                                <?php echo ucfirst($course['status']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px;">You are not enrolled in any courses yet</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>
