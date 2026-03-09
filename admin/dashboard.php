<?php
/**
 * Admin Dashboard - SIMS
 */

require_once '../config.php';
require_once '../database.php';
require_once '../auth.php';
require_once '../functions.php';

// Check permission
checkPermission(ROLE_ADMIN, $auth);

// Get dashboard statistics
$stats = array();

// Total Users
$result = $db->getConnection()->query("SELECT COUNT(*) as count FROM users");
$stats['total_users'] = $result->fetch_assoc()['count'];

// Total Students
$result = $db->getConnection()->query("SELECT COUNT(*) as count FROM students WHERE status = 'active'");
$stats['total_students'] = $result->fetch_assoc()['count'];

// Total Teachers
$result = $db->getConnection()->query("SELECT COUNT(*) as count FROM teachers WHERE status = 'active'");
$stats['total_teachers'] = $result->fetch_assoc()['count'];

// Total Courses
$result = $db->getConnection()->query("SELECT COUNT(*) as count FROM courses WHERE status = 'active'");
$stats['total_courses'] = $result->fetch_assoc()['count'];

// Total Enrollments
$result = $db->getConnection()->query("SELECT COUNT(*) as count FROM enrollments WHERE status = 'active'");
$stats['total_enrollments'] = $result->fetch_assoc()['count'];

// Get recent activities
$result = $db->getConnection()->query(
    "SELECT a.*, u.full_name FROM activity_log a 
     JOIN users u ON a.user_id = u.user_id 
     ORDER BY a.created_at DESC LIMIT 10"
);
$recent_activities = $result->fetch_all(MYSQLI_ASSOC);

$current_user = $auth->getCurrentUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SIMS</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .dashboard-container {
            padding: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #667eea;
        }

        .stat-card h3 {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .stat-card .number {
            font-size: 32px;
            font-weight: bold;
            color: #333;
        }

        .stat-card.blue {
            border-left-color: #667eea;
        }

        .stat-card.green {
            border-left-color: #48bb78;
        }

        .stat-card.orange {
            border-left-color: #f6ad55;
        }

        .stat-card.red {
            border-left-color: #f56565;
        }

        .stat-card.purple {
            border-left-color: #9f7aea;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }

        .activities-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .activities-table thead {
            background: #f7fafc;
        }

        .activities-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #e2e8f0;
        }

        .activities-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        .activities-table tbody tr:hover {
            background: #f7fafc;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 30px;
        }

        .action-btn {
            background: #667eea;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: background 0.3s;
        }

        .action-btn:hover {
            background: #5568d3;
        }

        .action-btn.secondary {
            background: #48bb78;
        }

        .action-btn.secondary:hover {
            background: #38a169;
        }
    </style>
</head>
<body>
    <?php require_once '../includes/navbar.php'; ?>
    
    <div class="dashboard-container">
        <div class="header">
            <h1>Welcome, <?php echo htmlspecialchars($current_user['full_name']); ?></h1>
            <p>Dashboard Overview</p>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card blue">
                <h3>Total Users</h3>
                <div class="number"><?php echo $stats['total_users']; ?></div>
            </div>

            <div class="stat-card green">
                <h3>Active Students</h3>
                <div class="number"><?php echo $stats['total_students']; ?></div>
            </div>

            <div class="stat-card orange">
                <h3>Active Teachers</h3>
                <div class="number"><?php echo $stats['total_teachers']; ?></div>
            </div>

            <div class="stat-card purple">
                <h3>Active Courses</h3>
                <div class="number"><?php echo $stats['total_courses']; ?></div>
            </div>

            <div class="stat-card red">
                <h3>Total Enrollments</h3>
                <div class="number"><?php echo $stats['total_enrollments']; ?></div>
            </div>
        </div>

        <!-- Quick Actions -->
        <h2 class="section-title">Quick Actions</h2>
        <div class="quick-actions">
            <a href="students.php" class="action-btn">Manage Students</a>
            <a href="teachers.php" class="action-btn secondary">Manage Teachers</a>
            <a href="courses.php" class="action-btn">Manage Courses</a>
            <a href="enrollments.php" class="action-btn secondary">Manage Enrollments</a>
            <a href="users.php" class="action-btn">Manage Users</a>
            <a href="reports.php" class="action-btn secondary">View Reports</a>
        </div>

        <!-- Recent Activities -->
        <h2 class="section-title">Recent Activities</h2>
        <table class="activities-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Action</th>
                    <th>Table</th>
                    <th>Details</th>
                    <th>Date/Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_activities as $activity): ?>
                <tr>
                    <td><?php echo htmlspecialchars($activity['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($activity['action']); ?></td>
                    <td><?php echo htmlspecialchars($activity['table_name'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($activity['details'] ?? '-'); ?></td>
                    <td><?php echo formatDateTime($activity['created_at']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>
