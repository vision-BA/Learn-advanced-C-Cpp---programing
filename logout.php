<?php
/**
 * Logout - SIMS
 */

require_once 'config.php';
require_once 'database.php';
require_once 'auth.php';

// Logout
$auth->logout();

// Redirect to login
header('Location: login.php?logout=1');
exit;
