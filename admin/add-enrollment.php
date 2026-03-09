<?php
/**
 * Add New Enrollment - SIMS
 */

require_once '../config.php';
require_once '../database.php';
require_once '../auth.php';
require_once '../functions.php';

// Check permission
checkPermission(ROLE_ADMIN, $auth);

$error = '';
$success = '';

// Get all active students
$result = $db->getConnection()->query(
    "SELECT s.student_id, u.full_name, s.registration_number FROM students s 
     JOIN users u ON s.user_id = u.user_id 
     WHERE s.status = 'active' ORDER BY u.full_name"
);
$students = $result->fetch_all(MYSQLI_ASSOC);

// Get all active courses
$result = $db->getConnection()->query(
    "SELECT course_id, course_code, course_name FROM courses 
     WHERE status = 'active' ORDER BY course_name"
);
$courses = $result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $student_id = (int)($_POST['student_id'] ?? 0);
    $course_id = (int)($_POST['course_id'] ?? 0);
    $enrollment_date = $_POST['enrollment_date'] ?? '';
    $status = $_POST['status'] ?? STATUS_ACTIVE;

    // Validation
    if ($student_id <= 0 || $course_id <= 0 || empty($enrollment_date)) {
        $error = 'All fields are required!';
    } else {
        // Check if enrollment already exists
        $stmt = $db->getConnection()->prepare(
            "SELECT enrollment_id FROM enrollments WHERE student_id = ? AND course_id = ?"
        );
        $stmt->bind_param('ii', $student_id, $course_id);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'Student is already enrolled in this course!';
            $stmt->close();
        } else {
            $stmt->close();
            
            // Insert enrollment
            $stmt = $db->getConnection()->prepare(
                "INSERT INTO enrollments (student_id, course_id, enrollment_date, status) 
                VALUES (?, ?, ?, ?)"
            );

            if ($stmt) {
                $stmt->bind_param('iiss', $student_id, $course_id, $enrollment_date, $status);
                
                if ($stmt->execute()) {
                    $enrollment_id = $db->getLastInsertId();
                    logActivity($db, $auth, 'CREATE', 'enrollments', $enrollment_id, 'New enrollment added');
                    header('Location: enrollments.php?success=Enrollment added successfully');
                    exit;
                } else {
                    $error = 'Failed to add enrollment';
                }
                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Enrollment - SIMS</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php require_once '../includes/navbar.php'; ?>

    <div class="form-container">
        <h1>Add New Enrollment</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label>Student <span class="required">*</span></label>
                    <select name="student_id" required>
                        <option value="">Select Student</option>
                        <?php foreach ($students as $student): ?>
                            <option value="<?php echo $student['student_id']; ?>" <?php echo (isset($_POST['student_id']) && $_POST['student_id'] == $student['student_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($student['full_name']) . ' (' . $student['registration_number'] . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Course <span class="required">*</span></label>
                    <select name="course_id" required>
                        <option value="">Select Course</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?php echo $course['course_id']; ?>" <?php echo (isset($_POST['course_id']) && $_POST['course_id'] == $course['course_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($course['course_code'] . ' - ' . $course['course_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Enrollment Date <span class="required">*</span></label>
                    <input type="date" name="enrollment_date" required value="<?php echo isset($_POST['enrollment_date']) ? $_POST['enrollment_date'] : date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="<?php echo STATUS_ACTIVE; ?>" <?php echo (isset($_POST['status']) && $_POST['status'] === STATUS_ACTIVE) ? 'selected' : 'selected'; ?>>Active</option>
                        <option value="completed" <?php echo (isset($_POST['status']) && $_POST['status'] === 'completed') ? 'selected' : ''; ?>>Completed</option>
                        <option value="dropped" <?php echo (isset($_POST['status']) && $_POST['status'] === 'dropped') ? 'selected' : ''; ?>>Dropped</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Enrollment</button>
                <a href="enrollments.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>
