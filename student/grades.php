<?php
/**
 * Student Grades - SIMS
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

// Get all grades
$result = $db->getConnection()->query(
    "SELECT g.*, c.course_code, c.course_name FROM grades g 
     JOIN courses c ON g.course_id = c.course_id 
     WHERE g.student_id = $student_id 
     ORDER BY g.created_at DESC"
);
$grades = $result->fetch_all(MYSQLI_ASSOC);

// Calculate overall GPA
$gpa = calculateGPA($db, $student_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Grades - SIMS</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php require_once '../includes/navbar.php'; ?>
    
    <div class="dashboard-container">
        <h1>My Grades</h1>

        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3>Overall GPA</h3>
            <div style="font-size: 32px; font-weight: bold; color: #667eea;">
                <?php echo number_format($gpa, 2); ?>
            </div>
        </div>

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
                    <th>Remarks</th>
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
                        <td><strong><?php echo $grade['total_mark'] ? number_format($grade['total_mark'], 2) : '-'; ?></strong></td>
                        <td>
                            <strong style="font-size: 18px; color: #667eea;"><?php echo $grade['grade'] ?? '-'; ?></strong>
                        </td>
                        <td><?php echo htmlspecialchars($grade['remarks'] ?? '-'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 20px;">No grades published yet</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>
