<?php
/**
 * Add New Course - SIMS
 */

require_once '../config.php';
require_once '../database.php';
require_once '../auth.php';
require_once '../functions.php';

// Check permission
checkPermission(ROLE_ADMIN, $auth);

$error = '';
$success = '';

// Get all teachers for dropdown
$result = $db->getConnection()->query(
    "SELECT t.teacher_id, u.full_name FROM teachers t 
     JOIN users u ON t.user_id = u.user_id 
     WHERE t.status = 'active' ORDER BY u.full_name"
);
$teachers = $result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $course_code = trim($_POST['course_code'] ?? '');
    $course_name = trim($_POST['course_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $credits = (int)($_POST['credits'] ?? 3);
    $class_level = trim($_POST['class_level'] ?? '');
    $teacher_id = !empty($_POST['teacher_id']) ? (int)$_POST['teacher_id'] : null;

    // Validation
    if (empty($course_code) || empty($course_name)) {
        $error = 'Course code and name are required!';
    } else {
        // Check if course code already exists
        $stmt = $db->getConnection()->prepare("SELECT course_id FROM courses WHERE course_code = ?");
        $stmt->bind_param('s', $course_code);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'Course code already exists!';
            $stmt->close();
        } else {
            $stmt->close();
            
            // Insert course
            $status = STATUS_ACTIVE;
            $stmt = $db->getConnection()->prepare(
                "INSERT INTO courses (course_code, course_name, description, credits, class_level, teacher_id, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            if ($stmt) {
                $teacher_id_param = $teacher_id ?? null;
                $stmt->bind_param('sssisss', $course_code, $course_name, $description, $credits, $class_level, $teacher_id_param, $status);
                
                if ($stmt->execute()) {
                    $course_id = $db->getLastInsertId();
                    logActivity($db, $auth, 'CREATE', 'courses', $course_id, 'New course added: ' . $course_name);
                    header('Location: courses.php?success=Course added successfully');
                    exit;
                } else {
                    $error = 'Failed to add course';
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
    <title>Add New Course - SIMS</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php require_once '../includes/navbar.php'; ?>

    <div class="form-container">
        <h1>Add New Course</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-section">
                <div class="form-section-title">Course Information</div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Course Code <span class="required">*</span></label>
                        <input type="text" name="course_code" required value="<?php echo isset($_POST['course_code']) ? htmlspecialchars($_POST['course_code']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Course Name <span class="required">*</span></label>
                        <input type="text" name="course_name" required value="<?php echo isset($_POST['course_name']) ? htmlspecialchars($_POST['course_name']) : ''; ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Class Level</label>
                        <input type="text" name="class_level" value="<?php echo isset($_POST['class_level']) ? htmlspecialchars($_POST['class_level']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Credits</label>
                        <input type="number" name="credits" value="<?php echo isset($_POST['credits']) ? (int)$_POST['credits'] : '3'; ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Instructor</label>
                        <select name="teacher_id">
                            <option value="">Select Instructor</option>
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?php echo $teacher['teacher_id']; ?>" <?php echo (isset($_POST['teacher_id']) && $_POST['teacher_id'] == $teacher['teacher_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($teacher['full_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Course</button>
                <a href="courses.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>
