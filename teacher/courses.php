<?php
/**
 * Teacher Courses - SIMS
 */

require_once '../config.php';
require_once '../database.php';
require_once '../auth.php';
require_once '../functions.php';

// Check permission
checkPermission(ROLE_TEACHER, $auth);

$user = $auth->getCurrentUser();

// Get teacher ID
$stmt = $db->getConnection()->prepare("SELECT teacher_id FROM teachers WHERE user_id = ?");
$stmt->bind_param('i', $user['user_id']);
$stmt->execute();
$teacher = $stmt->get_result()->fetch_assoc();
$stmt->close();
$teacher_id = $teacher['teacher_id'];

// Get all assigned courses
$result = $db->getConnection()->query(
    "SELECT * FROM courses WHERE teacher_id = $teacher_id ORDER BY course_name"
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
                    <th>Credits</th>
                    <th>Class Level</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($courses) > 0): ?>
                    <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                        <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                        <td><?php echo $course['credits']; ?></td>
                        <td><?php echo htmlspecialchars($course['class_level'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars(substr($course['description'] ?? '', 0, 50)) . '...'; ?></td>
                        <td>
                            <a href="grades.php?course=<?php echo $course['course_id']; ?>" class="btn btn-small">Grades</a>
                            <a href="attendance.php?course=<?php echo $course['course_id']; ?>" class="btn btn-small">Attendance</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px;">No courses assigned</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>
