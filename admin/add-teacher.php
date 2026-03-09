<?php
/**
 * Add New Teacher - SIMS
 */

require_once '../config.php';
require_once '../database.php';
require_once '../auth.php';
require_once '../functions.php';

// Check permission
checkPermission(ROLE_ADMIN, $auth);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $employee_id = trim($_POST['employee_id'] ?? '');
    $qualification = trim($_POST['qualification'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $postal_code = trim($_POST['postal_code'] ?? '');
    $date_of_joining = $_POST['date_of_joining'] ?? '';

    // Validation
    if (empty($username) || empty($email) || empty($full_name) || empty($password) || empty($employee_id) || empty($date_of_joining)) {
        $error = 'All required fields must be filled!';
    } elseif (!isValidEmail($email)) {
        $error = 'Invalid email address!';
    } else {
        // Create user account
        $hashed_password = Auth::hashPassword($password);
        $role = ROLE_TEACHER;
        
        $stmt = $db->getConnection()->prepare(
            "INSERT INTO users (username, email, full_name, password, role) VALUES (?, ?, ?, ?, ?)"
        );

        if ($stmt) {
            $stmt->bind_param('sssss', $username, $email, $full_name, $hashed_password, $role);
            
            if ($stmt->execute()) {
                $user_id = $db->getLastInsertId();
                
                // Insert teacher record
                $status = STATUS_ACTIVE;
                $stmt2 = $db->getConnection()->prepare(
                    "INSERT INTO teachers (user_id, employee_id, qualification, specialization, phone_number, 
                    address, city, state, postal_code, date_of_joining, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );

                if ($stmt2) {
                    $stmt2->bind_param('issssssssss', $user_id, $employee_id, $qualification, $specialization, 
                        $phone_number, $address, $city, $state, $postal_code, $date_of_joining, $status);
                    
                    if ($stmt2->execute()) {
                        $teacher_id = $db->getLastInsertId();
                        logActivity($db, $auth, 'CREATE', 'teachers', $teacher_id, 'New teacher added: ' . $full_name);
                        header('Location: teachers.php?success=Teacher added successfully');
                        exit;
                    } else {
                        $error = 'Failed to create teacher record';
                    }
                    $stmt2->close();
                }
            } else {
                if ($db->getConnection()->errno === 1062) {
                    $error = 'Username or email already exists!';
                } else {
                    $error = 'Failed to create user account';
                }
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Teacher - SIMS</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php require_once '../includes/navbar.php'; ?>

    <div class="form-container">
        <h1>Add New Teacher</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <!-- Account Information -->
            <div class="form-section">
                <div class="form-section-title">Account Information</div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Username <span class="required">*</span></label>
                        <input type="text" name="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Email <span class="required">*</span></label>
                        <input type="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name <span class="required">*</span></label>
                        <input type="text" name="full_name" required value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Password <span class="required">*</span></label>
                        <input type="password" name="password" required>
                    </div>
                </div>
            </div>

            <!-- Teacher Information -->
            <div class="form-section">
                <div class="form-section-title">Teacher Information</div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Employee ID <span class="required">*</span></label>
                        <input type="text" name="employee_id" required value="<?php echo isset($_POST['employee_id']) ? htmlspecialchars($_POST['employee_id']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Qualification</label>
                        <input type="text" name="qualification" value="<?php echo isset($_POST['qualification']) ? htmlspecialchars($_POST['qualification']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Specialization</label>
                        <input type="text" name="specialization" value="<?php echo isset($_POST['specialization']) ? htmlspecialchars($_POST['specialization']) : ''; ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone_number" value="<?php echo isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Date of Joining <span class="required">*</span></label>
                        <input type="date" name="date_of_joining" required value="<?php echo isset($_POST['date_of_joining']) ? $_POST['date_of_joining'] : ''; ?>">
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="form-section">
                <div class="form-section-title">Address Information</div>
                
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>State</label>
                        <input type="text" name="state" value="<?php echo isset($_POST['state']) ? htmlspecialchars($_POST['state']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Postal Code</label>
                        <input type="text" name="postal_code" value="<?php echo isset($_POST['postal_code']) ? htmlspecialchars($_POST['postal_code']) : ''; ?>">
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Teacher</button>
                <a href="teachers.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>
