<?php
/**
 * Teacher Attendance - SIMS
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

// Get selected course
$course_id = isset($_GET['course']) ? (int)$_GET['course'] : 0;
$attendance_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Get teacher's courses
$courses_result = $db->getConnection()->query(
    "SELECT course_id, course_code, course_name FROM courses WHERE teacher_id = $teacher_id ORDER BY course_name"
);
$courses = $courses_result->fetch_all(MYSQLI_ASSOC);

// Get enrolled students for selected course
$students = array();
if ($course_id > 0) {
    $result = $db->getConnection()->query(
        "SELECT e.student_id, u.full_name, s.registration_number 
         FROM enrollments e 
         JOIN students s ON e.student_id = s.student_id 
         JOIN users u ON s.user_id = u.user_id 
         JOIN courses c ON e.course_id = c.course_id 
         WHERE e.course_id = $course_id AND c.teacher_id = $teacher_id AND e.status = 'active' 
         ORDER BY u.full_name"
    );
    $students = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Attendance - SIMS</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php require_once '../includes/navbar.php'; ?>
    
    <div class="dashboard-container">
        <h1>Manage Attendance</h1>

        <!-- Course and Date Selection -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <form method="GET" style="display: grid; gap: 10px;">
                <label>Select Course</label>
                <select name="course" onchange="document.querySelector('form').submit()" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    <option value="">-- Select Course --</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?php echo $course['course_id']; ?>" <?php echo $course_id == $course['course_id'] ? 'selected' : ''; ?>>
                            <?php echo $course['course_code'] . ' - ' . $course['course_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <div>
                <label>Select Date</label>
                <form method="GET" style="display: flex; gap: 10px;">
                    <input type="hidden" name="course" value="<?php echo $course_id; ?>">
                    <input type="date" name="date" value="<?php echo $attendance_date; ?>" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; flex: 1;">
                    <button type="submit" class="btn btn-primary">View</button>
                </form>
            </div>
        </div>

        <?php if ($course_id > 0 && count($students) > 0): ?>

        <!-- Attendance Recording -->
        <form method="POST" style="background: white; padding: 20px; border-radius: 8px;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Reg. #</th>
                        <th>Attendance Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($student['registration_number']); ?></td>
                        <td>
                            <select name="attendance[<?php echo $student['student_id']; ?>]" style="padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                                <option value="">-- Select --</option>
                                <option value="present">Present</option>
                                <option value="absent">Absent</option>
                                <option value="late">Late</option>
                            </select>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div style="margin-top: 20px;">
                <button type="submit" class="btn btn-primary">Save Attendance</button>
            </div>
        </form>

        <?php elseif ($course_id == 0): ?>

        <div style="background: white; padding: 20px; border-radius: 8px; text-align: center;">
            <p>Please select a course to record attendance.</p>
        </div>

        <?php else: ?>

        <div style="background: white; padding: 20px; border-radius: 8px; text-align: center;">
            <p>No students enrolled in this course.</p>
        </div>

        <?php endif; ?>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>
