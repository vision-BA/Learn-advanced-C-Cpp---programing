<?php
/**
 * Session and Authentication Handler - SIMS
 */

require_once 'config.php';
require_once 'database.php';

class Auth {
    private $db;

    public function __construct($database) {
        $this->db = $database;
        session_start();
    }

    /**
     * Hash password using bcrypt
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => PASSWORD_HASH_COST]);
    }

    /**
     * Verify password against hash
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Login user
     */
    public function login($username, $password) {
        $stmt = $this->db->getConnection()->prepare(
            "SELECT user_id, username, password, email, full_name, role, status FROM users WHERE username = ? AND status = ?"
        );
        
        if (!$stmt) {
            return array('success' => false, 'message' => 'Database error');
        }

        $stmt->bind_param('ss', $username, $status);
        $status = STATUS_ACTIVE;
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (self::verifyPassword($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['login_time'] = time();

                // Log activity
                $this->logActivity($user['user_id'], 'LOGIN', null, null, 'User logged in');

                return array('success' => true, 'message' => 'Login successful', 'role' => $user['role']);
            } else {
                return array('success' => false, 'message' => 'Invalid password');
            }
        } else {
            return array('success' => false, 'message' => 'User not found');
        }

        $stmt->close();
    }

    /**
     * Logout user
     */
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            $this->logActivity($_SESSION['user_id'], 'LOGOUT', null, null, 'User logged out');
        }
        session_destroy();
        return true;
    }

    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && isset($_SESSION['role']);
    }

    /**
     * Check if session has expired
     */
    public function checkSessionTimeout() {
        if ($this->isLoggedIn()) {
            if (time() - $_SESSION['login_time'] > SESSION_TIMEOUT) {
                $this->logout();
                return false;
            }
            $_SESSION['login_time'] = time(); // Refresh timeout
            return true;
        }
        return false;
    }

    /**
     * Check user role
     */
    public function hasRole($required_role) {
        if (!$this->isLoggedIn()) {
            return false;
        }

        if (is_array($required_role)) {
            return in_array($_SESSION['role'], $required_role);
        }

        return $_SESSION['role'] === $required_role;
    }

    /**
     * Get current user info
     */
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return array(
                'user_id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'email' => $_SESSION['email'],
                'full_name' => $_SESSION['full_name'],
                'role' => $_SESSION['role']
            );
        }
        return null;
    }

    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(CSRF_TOKEN_LENGTH));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token
     */
    public static function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Log user activity
     */
    public function logActivity($user_id, $action, $table_name = null, $record_id = null, $details = null) {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        
        $stmt = $this->db->getConnection()->prepare(
            "INSERT INTO activity_log (user_id, action, table_name, record_id, details, ip_address) VALUES (?, ?, ?, ?, ?, ?)"
        );

        if ($stmt) {
            $stmt->bind_param('issiis', $user_id, $action, $table_name, $record_id, $details, $ip_address);
            $stmt->execute();
            $stmt->close();
        }
    }

    /**
     * Register new user
     */
    public function register($username, $email, $full_name, $password, $role = ROLE_STUDENT) {
        // Check if user already exists
        $stmt = $this->db->getConnection()->prepare(
            "SELECT user_id FROM users WHERE username = ? OR email = ?"
        );
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            return array('success' => false, 'message' => 'Username or email already exists');
        }
        $stmt->close();

        // Insert new user
        $hashed_password = self::hashPassword($password);
        $stmt = $this->db->getConnection()->prepare(
            "INSERT INTO users (username, email, full_name, password, role) VALUES (?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            return array('success' => false, 'message' => 'Database error');
        }

        $stmt->bind_param('sssss', $username, $email, $full_name, $hashed_password, $role);
        
        if ($stmt->execute()) {
            $user_id = $this->db->getLastInsertId();
            $this->logActivity($user_id, 'REGISTER', 'users', $user_id, 'New user registered');
            $stmt->close();
            return array('success' => true, 'message' => 'User registered successfully', 'user_id' => $user_id);
        } else {
            $stmt->close();
            return array('success' => false, 'message' => 'Registration failed');
        }
    }
}

// Initialize auth
$auth = new Auth($db);

// Check session timeout on every page load
if ($auth->isLoggedIn()) {
    if (!$auth->checkSessionTimeout()) {
        header('Location: login.php?session_expired=1');
        exit;
    }
}
