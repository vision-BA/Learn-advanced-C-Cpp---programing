<?php
/**
 * Reports - SIMS
 */

require_once '../config.php';
require_once '../database.php';
require_once '../auth.php';
require_once '../functions.php';

// Check permission
checkPermission(ROLE_ADMIN, $auth);

$report_type = $_GET['type'] ?? 'dashboard';

// Get various statistics
$total_students = $db->getConnection()->query("SELECT COUNT(*) as count FROM students WHERE status = 'active'")->fetch_assoc()['count'];
$total_teachers = $db->getConnection()->query("SELECT COUNT(*) as count FROM teachers WHERE status = 'active'")->fetch_assoc()['count'];
$total_courses = $db->getConnection()->query("SELECT COUNT(*) as count FROM courses WHERE status = 'active'")->fetch_assoc()['count'];
$total_enrollments = $db->getConnection()->query("SELECT COUNT(*) as count FROM enrollments WHERE status = 'active'")->fetch_assoc()['count'];

// Get top performing students
$top_students = $db->getConnection()->query(
    "SELECT u.full_name, AVG(g.total_mark) as avg_mark 
     FROM grades g 
     JOIN students s ON g.student_id = s.student_id 
     JOIN users u ON s.user_id = u.user_id 
     WHERE g.total_mark IS NOT NULL 
     GROUP BY g.student_id 
     ORDER BY avg_mark DESC LIMIT 10"
)->fetch_all(MYSQLI_ASSOC);

// Get course-wise enrollment
$course_enrollments = $db->getConnection()->query(
    "SELECT c.course_code, c.course_name, COUNT(e.enrollment_id) as enrollment_count 
     FROM courses c 
     LEFT JOIN enrollments e ON c.course_id = e.course_id AND e.status = 'active' 
     GROUP BY c.course_id 
     ORDER BY enrollment_count DESC LIMIT 10"
)->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - SIMS</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .report-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
        }

        .report-tabs a {
            padding: 10px 20px;
            text-decoration: none;
            color: #667eea;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }

        .report-tabs a.active {
            color: white;
            background: #667eea;
            border-bottom-color: #667eea;
        }

        .report-section {
            margin-bottom: 40px;
        }
    </style>
</head>
<body>
    <?php require_once '../includes/navbar.php'; ?>
    
    <div class="dashboard-container">
        <h1>Reports & Analytics</h1>

        <!-- Report Navigation -->
        <div class="report-tabs">
            <a href="?type=dashboard" class="<?php echo $report_type === 'dashboard' ? 'active' : ''; ?>">Dashboard</a>
            <a href="?type=students" class="<?php echo $report_type === 'students' ? 'active' : ''; ?>">Top Students</a>
            <a href="?type=courses" class="<?php echo $report_type === 'courses' ? 'active' : ''; ?>">Courses</a>
            <a href="?type=attendance" class="<?php echo $report_type === 'attendance' ? 'active' : ''; ?>">Attendance</a>
        </div>

        <?php if ($report_type === 'dashboard'): ?>

        <!-- Dashboard Report -->
        <div class="report-section">
            <h2 class="section-title">System Overview</h2>
            <div class="stats-grid">
                <div class="stat-card blue">
                    <h3>Active Students</h3>
                    <div class="number"><?php echo $total_students; ?></div>
                </div>
                <div class="stat-card green">
                    <h3>Active Teachers</h3>
                    <div class="number"><?php echo $total_teachers; ?></div>
                </div>
                <div class="stat-card orange">
                    <h3>Active Courses</h3>
                    <div class="number"><?php echo $total_courses; ?></div>
                </div>
                <div class="stat-card purple">
                    <h3>Active Enrollments</h3>
                    <div class="number"><?php echo $total_enrollments; ?></div>
                </div>
            </div>
        </div>

        <?php elseif ($report_type === 'students'): ?>

        <!-- Top Students Report -->
        <div class="report-section">
            <h2 class="section-title">Top 10 Performing Students</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Student Name</th>
                        <th>Average Mark</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $rank = 1; ?>
                    <?php foreach ($top_students as $student): ?>
                    <tr>
                        <td><?php echo $rank++; ?></td>
                        <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                        <td>
                            <strong><?php echo number_format($student['avg_mark'], 2); ?></strong>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php elseif ($report_type === 'courses'): ?>

        <!-- Course Enrollment Report -->
        <div class="report-section">
            <h2 class="section-title">Course Enrollment Report</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Active Enrollments</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($course_enrollments as $course): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                        <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                        <td><strong><?php echo $course['enrollment_count']; ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php elseif ($report_type === 'attendance'): ?>

        <!-- Attendance Report -->
        <div class="report-section">
            <h2 class="section-title">Attendance Summary</h2>
            <p>Attendance report functionality will be added here.</p>
            <p>This section will show attendance statistics, low attendance alerts, and per-class attendance reports.</p>
        </div>

        <?php endif; ?>

    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>
