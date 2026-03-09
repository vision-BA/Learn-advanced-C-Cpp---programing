<?php
/**
 * Student Dashboard - SIMS
 */

require_once '../config.php';
require_once '../database.php';
require_once '../auth.php';
require_once '../functions.php';

// Check permission
checkPermission(ROLE_STUDENT, $auth);

$user = $auth->getCurrentUser();

// Get student info
$stmt = $db->getConnection()->prepare(
    "SELECT * FROM students WHERE user_id = ?"
);
$stmt->bind_param('i', $user['user_id']);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();
$student_id = $student['student_id'];

// Get enrolled courses
$result = $db->getConnection()->query(
    "SELECT e.*, c.course_code, c.course_name, u.full_name as teacher_name 
     FROM enrollments e 
     JOIN courses c ON e.course_id = c.course_id 
     LEFT JOIN teachers t ON c.teacher_id = t.teacher_id 
     LEFT JOIN users u ON t.user_id = u.user_id 
     WHERE e.student_id = $student_id AND e.status = 'active' 
     LIMIT 6"
);
$courses = $result->fetch_all(MYSQLI_ASSOC);

// Get grades
$result = $db->getConnection()->query(
    "SELECT g.*, c.course_code, c.course_name 
     FROM grades g 
     JOIN courses c ON g.course_id = c.course_id 
     WHERE g.student_id = $student_id LIMIT 5"
);
$grades = $result->fetch_all(MYSQLI_ASSOC);

// Calculate GPA
$gpa = calculateGPA($db, $student_id);

// Get attendance
$attendance_percentage = calculateAttendancePercentage($db, $student_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - SIMS</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php require_once '../includes/navbar.php'; ?>
    
    <div class="dashboard-container">
        <div class="header">
            <h1>Welcome, <?php echo htmlspecialchars($user['full_name']); ?></h1>
            <p>Student Dashboard</p>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card green">
                <h3>GPA</h3>
                <div class="number"><?php echo number_format($gpa, 2); ?></div>
            </div>

            <div class="stat-card blue">
                <h3>Enrolled Courses</h3>
                <div class="number"><?php echo count($courses); ?></div>
            </div>

            <div class="stat-card orange">
                <h3>Attendance</h3>
                <div class="number"><?php echo number_format($attendance_percentage, 1); ?>%</div>
            </div>

            <div class="stat-card purple">
                <h3>Registration #</h3>
                <div class="number" style="font-size: 16px;"><?php echo htmlspecialchars($student['registration_number']); ?></div>
            </div>
        </div>

        <!-- Enrolled Courses -->
        <h2 class="section-title">My Courses</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Instructor</th>
                    <th>Enrollment Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($courses) > 0): ?>
                    <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                        <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                        <td><?php echo htmlspecialchars($course['teacher_name'] ?? '-'); ?></td>
                        <td><?php echo formatDate($course['enrollment_date']); ?></td>
                        <td>
                            <span class="badge badge-success"><?php echo ucfirst($course['status']); ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px;">No enrolled courses yet</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Recent Grades -->
        <h2 class="section-title">Recent Grades</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Assignment</th>
                    <th>Midterm</th>
                    <th>Final</th>
                    <th>Total</th>
                    <th>Grade</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($grades) > 0): ?>
                    <?php foreach ($grades as $grade): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($grade['course_code']); ?></td>
                        <td><?php echo htmlspecialchars($grade['course_name']); ?></td>
                        <td><?php echo $grade['assignment_mark'] ? number_format($grade['assignment_mark'], 2) : '-'; ?></td>
                        <td><?php echo $grade['midterm_mark'] ? number_format($grade['midterm_mark'], 2) : '-'; ?></td>
                        <td><?php echo $grade['final_mark'] ? number_format($grade['final_mark'], 2) : '-'; ?></td>
                        <td><?php echo $grade['total_mark'] ? number_format($grade['total_mark'], 2) : '-'; ?></td>
                        <td>
                            <strong><?php echo $grade['grade'] ?? '-'; ?></strong>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px;">No grades published yet</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Quick Actions -->
        <h2 class="section-title">Quick Actions</h2>
        <div class="quick-actions">
            <a href="courses.php" class="action-btn">View All Courses</a>
            <a href="grades.php" class="action-btn secondary">View All Grades</a>
            <a href="../profile.php" class="action-btn">Update Profile</a>
            <a href="../logout.php" class="action-btn secondary">Logout</a>
        </div>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>
