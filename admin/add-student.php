<?php
/**
 * Add New Student - SIMS
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
    $registration_number = trim($_POST['registration_number'] ?? '');
    $date_of_birth = $_POST['date_of_birth'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $phone_number = trim($_POST['phone_number'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $postal_code = trim($_POST['postal_code'] ?? '');
    $parent_name = trim($_POST['parent_name'] ?? '');
    $parent_phone = trim($_POST['parent_phone'] ?? '');
    $admission_date = $_POST['admission_date'] ?? '';
    $class_level = trim($_POST['class_level'] ?? '');

    // Validation
    if (empty($username) || empty($email) || empty($full_name) || empty($password) || 
        empty($registration_number) || empty($date_of_birth) || empty($gender) || empty($admission_date)) {
        $error = 'All required fields must be filled!';
    } elseif (!isValidEmail($email)) {
        $error = 'Invalid email address!';
    } else {
        // Create user account
        $hashed_password = Auth::hashPassword($password);
        $role = ROLE_STUDENT;
        
        $stmt = $db->getConnection()->prepare(
            "INSERT INTO users (username, email, full_name, password, role) VALUES (?, ?, ?, ?, ?)"
        );

        if ($stmt) {
            $stmt->bind_param('sssss', $username, $email, $full_name, $hashed_password, $role);
            
            if ($stmt->execute()) {
                $user_id = $db->getLastInsertId();
                
                // Insert student record
                $status = STATUS_ACTIVE;
                $stmt2 = $db->getConnection()->prepare(
                    "INSERT INTO students (user_id, registration_number, date_of_birth, gender, phone_number, 
                    address, city, state, postal_code, parent_name, parent_phone, admission_date, class_level, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );

                if ($stmt2) {
                    $stmt2->bind_param('isssssssssssss', $user_id, $registration_number, $date_of_birth, $gender, 
                        $phone_number, $address, $city, $state, $postal_code, $parent_name, $parent_phone, 
                        $admission_date, $class_level, $status);
                    
                    if ($stmt2->execute()) {
                        $student_id = $db->getLastInsertId();
                        logActivity($db, $auth, 'CREATE', 'students', $student_id, 'New student added: ' . $full_name);
                        header('Location: students.php?success=Student added successfully');
                        exit;
                    } else {
                        $error = 'Failed to create student record';
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
    <title>Add New Student - SIMS</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-section-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }

        .required {
            color: red;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="date"],
        select,
        textarea {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            font-family: inherit;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="date"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #333;
        }

        .btn-secondary:hover {
            background: #cbd5e0;
        }

        .alert {
            padding: 12px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }

        .alert-success {
            background: #efe;
            color: #3c3;
            border: 1px solid #cfc;
        }
    </style>
</head>
<body>
    <?php require_once '../includes/navbar.php'; ?>

    <div class="form-container">
        <h1>Add New Student</h1>

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

            <!-- Personal Information -->
            <div class="form-section">
                <div class="form-section-title">Personal Information</div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Registration Number <span class="required">*</span></label>
                        <input type="text" name="registration_number" required value="<?php echo isset($_POST['registration_number']) ? htmlspecialchars($_POST['registration_number']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Date of Birth <span class="required">*</span></label>
                        <input type="date" name="date_of_birth" required value="<?php echo isset($_POST['date_of_birth']) ? $_POST['date_of_birth'] : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Gender <span class="required">*</span></label>
                        <select name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone_number" value="<?php echo isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Class Level</label>
                        <input type="text" name="class_level" value="<?php echo isset($_POST['class_level']) ? htmlspecialchars($_POST['class_level']) : ''; ?>">
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

            <!-- Parent Information -->
            <div class="form-section">
                <div class="form-section-title">Parent/Guardian Information</div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Parent/Guardian Name</label>
                        <input type="text" name="parent_name" value="<?php echo isset($_POST['parent_name']) ? htmlspecialchars($_POST['parent_name']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Parent/Guardian Phone</label>
                        <input type="text" name="parent_phone" value="<?php echo isset($_POST['parent_phone']) ? htmlspecialchars($_POST['parent_phone']) : ''; ?>">
                    </div>
                </div>
            </div>

            <!-- Admission Information -->
            <div class="form-section">
                <div class="form-section-title">Admission Information</div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Admission Date <span class="required">*</span></label>
                        <input type="date" name="admission_date" required value="<?php echo isset($_POST['admission_date']) ? $_POST['admission_date'] : ''; ?>">
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Student</button>
                <a href="students.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>
