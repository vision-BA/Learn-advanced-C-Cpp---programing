<?php
/**
 * Teacher Dashboard - SIMS
 */

require_once '../config.php';
require_once '../database.php';
require_once '../auth.php';
require_once '../functions.php';

// Check permission
checkPermission(ROLE_TEACHER, $auth);

$user = $auth->getCurrentUser();

// Get teacher info
$stmt = $db->getConnection()->prepare(
    "SELECT * FROM teachers WHERE user_id = ?"
);
$stmt->bind_param('i', $user['user_id']);
$stmt->execute();
$teacher = $stmt->get_result()->fetch_assoc();
$stmt->close();
$teacher_id = $teacher['teacher_id'];

// Get assigned courses
$result = $db->getConnection()->query(
    "SELECT * FROM courses WHERE teacher_id = $teacher_id AND status = 'active'"
);
$courses = $result->fetch_all(MYSQLI_ASSOC);

// Get total students taught
$result = $db->getConnection()->query(
    "SELECT COUNT(DISTINCT e.student_id) as total FROM enrollments e 
     JOIN courses c ON e.course_id = c.course_id 
     WHERE c.teacher_id = $teacher_id"
);
$student_count = $result->fetch_assoc()['total'];

// Get recent grades submitted
$result = $db->getConnection()->query(
    "SELECT g.*, c.course_code, c.course_name, u.full_name as student_name
     FROM grades g 
     JOIN courses c ON g.course_id = c.course_id 
     JOIN students s ON g.student_id = s.student_id 
     JOIN users u ON s.user_id = u.user_id 
     WHERE c.teacher_id = $teacher_id 
     ORDER BY g.updated_at DESC LIMIT 10"
);
$grades = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - SIMS</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php require_once '../includes/navbar.php'; ?>
    
    <div class="dashboard-container">
        <div class="header">
            <h1>Welcome, <?php echo htmlspecialchars($user['full_name']); ?></h1>
            <p>Teacher Dashboard</p>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card blue">
                <h3>Assigned Courses</h3>
                <div class="number"><?php echo count($courses); ?></div>
            </div>

            <div class="stat-card green">
                <h3>Total Students Taught</h3>
                <div class="number"><?php echo $student_count; ?></div>
            </div>

            <div class="stat-card orange">
                <h3>Qualification</h3>
                <div class="number" style="font-size: 14px;"><?php echo htmlspecialchars($teacher['qualification'] ?? 'N/A'); ?></div>
            </div>

            <div class="stat-card purple">
                <h3>Specialization</h3>
                <div class="number" style="font-size: 14px;"><?php echo htmlspecialchars($teacher['specialization'] ?? 'N/A'); ?></div>
            </div>
        </div>

        <!-- Assigned Courses -->
        <h2 class="section-title">My Courses</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Credits</th>
                    <th>Class Level</th>
                    <th>Status</th>
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
                        <td>
                            <span class="badge badge-success"><?php echo ucfirst($course['status']); ?></span>
                        </td>
                        <td>
                            <a href="grades.php?course=<?php echo $course['course_id']; ?>" class="btn btn-small">View Grades</a>
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

        <!-- Recent Grades Submitted -->
        <h2 class="section-title">Recent Grades Submitted</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Course Code</th>
                    <th>Total Mark</th>
                    <th>Grade</th>
                    <th>Submitted</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($grades) > 0): ?>
                    <?php foreach ($grades as $grade): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($grade['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($grade['course_code']); ?></td>
                        <td><?php echo $grade['total_mark'] ? number_format($grade['total_mark'], 2) : '-'; ?></td>
                        <td>
                            <strong><?php echo $grade['grade'] ?? '-'; ?></strong>
                        </td>
                        <td><?php echo formatDateTime($grade['updated_at']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px;">No grades submitted yet</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Quick Actions -->
        <h2 class="section-title">Quick Actions</h2>
        <div class="quick-actions">
            <a href="courses.php" class="action-btn">View All Courses</a>
            <a href="grades.php" class="action-btn secondary">Manage Grades</a>
            <a href="attendance.php" class="action-btn">Attendance Records</a>
            <a href="../profile.php" class="action-btn secondary">Update Profile</a>
        </div>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>
