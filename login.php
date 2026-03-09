<?php
/**
 * Login Page - SIMS
 */

require_once 'config.php';
require_once 'database.php';
require_once 'auth.php';

$error_message = '';
$success_message = '';

// Check if already logged in
if ($auth->isLoggedIn()) {
    if ($_SESSION['role'] === ROLE_ADMIN) {
        header('Location: admin/dashboard.php');
    } elseif ($_SESSION['role'] === ROLE_TEACHER) {
        header('Location: teacher/dashboard.php');
    } else {
        header('Location: student/dashboard.php');
    }
    exit;
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error_message = 'Username and password are required!';
        } else {
            $login_result = $auth->login($username, $password);
            
            if ($login_result['success']) {
                $success_message = 'Login successful!';
                
                if ($login_result['role'] === ROLE_ADMIN) {
                    header('Location: admin/dashboard.php');
                } elseif ($login_result['role'] === ROLE_TEACHER) {
                    header('Location: teacher/dashboard.php');
                } else {
                    header('Location: student/dashboard.php');
                }
                exit;
            } else {
                $error_message = $login_result['message'];
            }
        }
    }
}

// Check for session expired message
$session_expired = isset($_GET['session_expired']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            padding: 40px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 5px;
        }

        .login-header p {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin: 15px 0;
        }

        input[type="checkbox"] {
            margin-right: 8px;
            cursor: pointer;
        }

        .remember-me label {
            margin: 0;
            cursor: pointer;
            font-weight: 400;
            font-size: 14px;
        }

        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        button[type="submit"]:active {
            transform: translateY(0);
        }

        .message {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .demo-credentials {
            background-color: #e2e3e5;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 13px;
            color: #383d41;
        }

        .demo-credentials strong {
            display: block;
            margin-bottom: 5px;
        }

        .demo-credentials code {
            background: white;
            padding: 2px 5px;
            border-radius: 3px;
            font-family: monospace;
        }

        .forgot-password {
            text-align: center;
            margin-top: 15px;
        }

        .forgot-password a {
            color: #667eea;
            text-decoration: none;
            font-size: 13px;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>SIMS</h1>
            <p>Student Information Management System</p>
        </div>

        <?php if ($session_expired): ?>
            <div class="message info">
                Your session has expired. Please login again.
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="message error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="message success">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me</label>
            </div>

            <button type="submit" name="login" value="1">Login</button>
        </form>

        <div class="demo-credentials">
            <strong>Demo Credentials:</strong>
            <p>Username: <code>admin</code></p>
            <p>Password: <code>admin123</code></p>
        </div>

        <div class="forgot-password">
            <a href="forgot-password.php">Forgot password?</a>
        </div>
    </div>
</body>
</html>
