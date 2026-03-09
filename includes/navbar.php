<?php
/**
 * Navigation Bar - SIMS
 */

$current_page = basename($_SERVER['PHP_SELF']);
$user = $auth->getCurrentUser();
$unread_notifications = getUnreadNotificationsCount($db, $user['user_id']);
?>

<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-brand">
            <h2>SIMS</h2>
        </div>

        <ul class="navbar-menu">
            <?php if ($user['role'] === ROLE_ADMIN): ?>
                <li>
                    <a href="../admin/dashboard.php" class="<?php echo strpos($current_page, 'admin') !== false && $current_page === 'dashboard.php' ? 'active' : ''; ?>">
                        Dashboard
                    </a>
                </li>
                <li class="dropdown">
                    <a href="#">Management</a>
                    <ul class="dropdown-menu">
                        <li><a href="../admin/students.php">Students</a></li>
                        <li><a href="../admin/teachers.php">Teachers</a></li>
                        <li><a href="../admin/courses.php">Courses</a></li>
                        <li><a href="../admin/enrollments.php">Enrollments</a></li>
                        <li><a href="../admin/users.php">Users</a></li>
                    </ul>
                </li>
                <li><a href="../admin/reports.php">Reports</a></li>
            <?php elseif ($user['role'] === ROLE_TEACHER): ?>
                <li><a href="../teacher/dashboard.php">Dashboard</a></li>
                <li><a href="../teacher/courses.php">Courses</a></li>
                <li><a href="../teacher/grades.php">Grades</a></li>
                <li><a href="../teacher/attendance.php">Attendance</a></li>
            <?php else: ?>
                <li><a href="../student/dashboard.php">Dashboard</a></li>
                <li><a href="../student/courses.php">My Courses</a></li>
                <li><a href="../student/grades.php">My Grades</a></li>
            <?php endif; ?>
        </ul>

        <div class="navbar-right">
            <div class="notifications">
                <a href="#" class="notification-bell">
                    🔔
                    <?php if ($unread_notifications > 0): ?>
                        <span class="notification-badge"><?php echo $unread_notifications; ?></span>
                    <?php endif; ?>
                </a>
            </div>

            <div class="user-menu">
                <span><?php echo htmlspecialchars($user['full_name']); ?></span>
                <div class="dropdown-menu">
                    <a href="profile.php">Profile</a>
                    <a href="settings.php">Settings</a>
                    <hr>
                    <a href="#logout" onclick="logout()">Logout</a>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
function logout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = '../logout.php';
    }
}
</script>

<style>
    .navbar {
        background: #667eea;
        color: white;
        padding: 0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .navbar-container {
        max-width: 100%;
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 60px;
    }

    .navbar-brand h2 {
        margin: 0;
        font-size: 24px;
    }

    .navbar-menu {
        list-style: none;
        display: flex;
        margin: 0;
        padding: 0;
        gap: 20px;
    }

    .navbar-menu a {
        color: white;
        text-decoration: none;
        padding: 15px 10px;
        display: block;
        transition: background 0.3s;
    }

    .navbar-menu a:hover,
    .navbar-menu a.active {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 5px;
    }

    .dropdown {
        position: relative;
    }

    .dropdown-menu {
        list-style: none;
        background: white;
        color: #333;
        position: absolute;
        top: 100%;
        left: 0;
        min-width: 150px;
        padding: 10px 0;
        border-radius: 5px;
        display: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .dropdown:hover .dropdown-menu {
        display: block;
    }

    .dropdown-menu li {
        padding: 0;
    }

    .dropdown-menu a {
        padding: 10px 20px;
        color: #333;
        display: block;
    }

    .dropdown-menu a:hover {
        background: #f0f0f0;
    }

    .navbar-right {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .notification-bell {
        position: relative;
        font-size: 20px;
    }

    .notification-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #f56565;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
    }

    .user-menu {
        position: relative;
        cursor: pointer;
    }
</style>
