<?php
/**
 * Teacher Grades - SIMS
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

// Get teacher's courses
$courses_result = $db->getConnection()->query(
    "SELECT course_id, course_code, course_name FROM courses WHERE teacher_id = $teacher_id ORDER BY course_name"
);
$courses = $courses_result->fetch_all(MYSQLI_ASSOC);

// Get grades for selected course
$grades = array();
if ($course_id > 0) {
    $result = $db->getConnection()->query(
        "SELECT g.*, s.registration_number, u.full_name as student_name, c.course_name 
         FROM grades g 
         JOIN students s ON g.student_id = s.student_id 
         JOIN users u ON s.user_id = u.user_id 
         JOIN courses c ON g.course_id = c.course_id 
         WHERE g.course_id = $course_id AND c.teacher_id = $teacher_id"
    );
    $grades = $result->fetch_all(MYSQLI_ASSOC);
}

// Handle grade submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_grades'])) {
    $grade_id = (int)$_POST['grade_id'];
    $assignment_mark = !empty($_POST['assignment_mark']) ? (float)$_POST['assignment_mark'] : null;
    $midterm_mark = !empty($_POST['midterm_mark']) ? (float)$_POST['midterm_mark'] : null;
    $final_mark = !empty($_POST['final_mark']) ? (float)$_POST['final_mark'] : null;
    
    // Calculate total
    $total_mark = 0;
    if ($assignment_mark) $total_mark += $assignment_mark;
    if ($midterm_mark) $total_mark += $midterm_mark;
    if ($final_mark) $total_mark += $final_mark;
    
    $total_mark = $total_mark > 0 ? $total_mark / 3 : null;
    
    // Update grade
    $stmt = $db->getConnection()->prepare(
        "UPDATE grades SET assignment_mark = ?, midterm_mark = ?, final_mark = ?, total_mark = ? WHERE grade_id = ?"
    );
    
    if ($stmt) {
        $stmt->bind_param('ddddi', $assignment_mark, $midterm_mark, $final_mark, $total_mark, $grade_id);
        $stmt->execute();
        $stmt->close();
        header("Location: grades.php?course=$course_id&message=Grades updated");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Grades - SIMS</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php require_once '../includes/navbar.php'; ?>
    
    <div class="dashboard-container">
        <h1>Manage Grades</h1>

        <!-- Course Selection -->
        <div style="margin-bottom: 20px;">
            <form method="GET" style="display: flex; gap: 10px;">
                <select name="course" onchange="this.form.submit()" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    <option value="">Select a Course</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?php echo $course['course_id']; ?>" <?php echo $course_id == $course['course_id'] ? 'selected' : ''; ?>>
                            <?php echo $course['course_code'] . ' - ' . $course['course_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <?php if ($course_id > 0): ?>

        <!-- Grades Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Reg. #</th>
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
                        <td><?php echo htmlspecialchars($grade['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($grade['registration_number']); ?></td>
                        <td><?php echo $grade['assignment_mark'] ? number_format($grade['assignment_mark'], 2) : '-'; ?></td>
                        <td><?php echo $grade['midterm_mark'] ? number_format($grade['midterm_mark'], 2) : '-'; ?></td>
                        <td><?php echo $grade['final_mark'] ? number_format($grade['final_mark'], 2) : '-'; ?></td>
                        <td><?php echo $grade['total_mark'] ? number_format($grade['total_mark'], 2) : '-'; ?></td>
                        <td><?php echo $grade['grade'] ?? '-'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px;">No grades found for this course</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php else: ?>

        <div style="background: white; padding: 20px; border-radius: 8px; text-align: center;">
            <p>Please select a course to view and manage grades.</p>
        </div>

        <?php endif; ?>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>
