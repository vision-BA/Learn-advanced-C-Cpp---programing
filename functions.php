<?php
/**
 * Utility Functions - SIMS
 */

/**
 * Redirect to a page
 */
function redirect($page) {
    header("Location: $page");
    exit;
}

/**
 * Get student name by ID
 */
function getStudentName($db, $student_id) {
    $stmt = $db->getConnection()->prepare(
        "SELECT s.*, u.full_name FROM students s 
         JOIN users u ON s.user_id = u.user_id 
         WHERE s.student_id = ?"
    );
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();
    return $student ? $student['full_name'] : 'Unknown';
}

/**
 * Get teacher name by ID
 */
function getTeacherName($db, $teacher_id) {
    $stmt = $db->getConnection()->prepare(
        "SELECT t.*, u.full_name FROM teachers t 
         JOIN users u ON t.user_id = u.user_id 
         WHERE t.teacher_id = ?"
    );
    $stmt->bind_param('i', $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $teacher = $result->fetch_assoc();
    $stmt->close();
    return $teacher ? $teacher['full_name'] : 'Unknown';
}

/**
 * Get course name by ID
 */
function getCourseName($db, $course_id) {
    $stmt = $db->getConnection()->prepare(
        "SELECT course_name FROM courses WHERE course_id = ?"
    );
    $stmt->bind_param('i', $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $course = $result->fetch_assoc();
    $stmt->close();
    return $course ? $course['course_name'] : 'Unknown';
}

/**
 * Get grade letter from marks
 */
function getGradeLetter($marks, $grades_scale) {
    foreach ($grades_scale as $score => $grade) {
        if ($marks >= (int)$score) {
            return $grade;
        }
    }
    return 'F';
}

/**
 * Format date
 */
function formatDate($date) {
    if (empty($date) || $date === '0000-00-00') {
        return 'N/A';
    }
    return date('d-m-Y', strtotime($date));
}

/**
 * Format datetime
 */
function formatDateTime($datetime) {
    if (empty($datetime) || $datetime === '0000-00-00 00:00:00') {
        return 'N/A';
    }
    return date('d-m-Y H:i:s', strtotime($datetime));
}

/**
 * Sanitize input
 */
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone
 */
function isValidPhone($phone) {
    return preg_match('/^[0-9]{10,15}$/', preg_replace('/[^\d]/', '', $phone));
}

/**
 * Check permission
 */
function checkPermission($required_roles, $auth) {
    if (!$auth->isLoggedIn()) {
        redirect('login.php');
    }
    
    if (!$auth->hasRole($required_roles)) {
        die('Access denied. You do not have permission to access this page.');
    }
}

/**
 * Paginate results
 */
function paginate($total_items, $items_per_page = ITEMS_PER_PAGE) {
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $total_pages = ceil($total_items / $items_per_page);
    
    if ($current_page < 1) {
        $current_page = 1;
    } elseif ($current_page > $total_pages) {
        $current_page = $total_pages;
    }
    
    $offset = ($current_page - 1) * $items_per_page;
    
    return array(
        'current_page' => $current_page,
        'total_pages' => $total_pages,
        'total_items' => $total_items,
        'offset' => $offset,
        'limit' => $items_per_page
    );
}

/**
 * Generate pagination HTML
 */
function generatePaginationHTML($pagination, $base_url) {
    if ($pagination['total_pages'] <= 1) {
        return '';
    }

    $html = '<div class="pagination">';
    
    // Previous button
    if ($pagination['current_page'] > 1) {
        $html .= '<a href="' . $base_url . '?page=' . ($pagination['current_page'] - 1) . '" class="page-link">Previous</a>';
    }
    
    // Page numbers
    for ($i = 1; $i <= $pagination['total_pages']; $i++) {
        if ($i === $pagination['current_page']) {
            $html .= '<span class="page-link active">' . $i . '</span>';
        } else {
            $html .= '<a href="' . $base_url . '?page=' . $i . '" class="page-link">' . $i . '</a>';
        }
    }
    
    // Next button
    if ($pagination['current_page'] < $pagination['total_pages']) {
        $html .= '<a href="' . $base_url . '?page=' . ($pagination['current_page'] + 1) . '" class="page-link">Next</a>';
    }
    
    $html .= '</div>';
    return $html;
}

/**
 * Log activity
 */
function logActivity($db, $auth, $action, $table_name = null, $record_id = null, $details = null) {
    if ($auth->isLoggedIn()) {
        $user = $auth->getCurrentUser();
        $auth->logActivity($user['user_id'], $action, $table_name, $record_id, $details);
    }
}

/**
 * Send notification
 */
function sendNotification($db, $user_id, $title, $message, $type = 'info') {
    $stmt = $db->getConnection()->prepare(
        "INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)"
    );
    
    if ($stmt) {
        $stmt->bind_param('isss', $user_id, $title, $message, $type);
        $stmt->execute();
        $stmt->close();
        return true;
    }
    return false;
}

/**
 * Get unread notifications count
 */
function getUnreadNotificationsCount($db, $user_id) {
    $stmt = $db->getConnection()->prepare(
        "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = FALSE"
    );
    
    if ($stmt) {
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['count'];
    }
    return 0;
}

/**
 * Calculate attendance percentage
 */
function calculateAttendancePercentage($db, $student_id, $course_id = null) {
    if ($course_id) {
        $stmt = $db->getConnection()->prepare(
            "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present
            FROM attendance 
            WHERE student_id = ? AND course_id = ? AND status IN ('present', 'absent', 'late')"
        );
        $stmt->bind_param('ii', $student_id, $course_id);
    } else {
        $stmt = $db->getConnection()->prepare(
            "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present
            FROM attendance 
            WHERE student_id = ? AND status IN ('present', 'absent', 'late')"
        );
        $stmt->bind_param('i', $student_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    if ($row['total'] == 0) {
        return 0;
    }
    
    return round(($row['present'] / $row['total']) * 100, 2);
}

/**
 * Calculate GPA
 */
function calculateGPA($db, $student_id) {
    $stmt = $db->getConnection()->prepare(
        "SELECT AVG(total_mark) as gpa FROM grades 
         WHERE student_id = ? AND total_mark IS NOT NULL"
    );
    
    if ($stmt) {
        $stmt->bind_param('i', $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['gpa'] ? round($row['gpa'], 2) : 0;
    }
    return 0;
}
