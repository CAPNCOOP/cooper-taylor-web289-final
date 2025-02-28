<?php
require_once 'private/initialize.php';

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Destroy session data
$_SESSION = [];
session_destroy();

// Regenerate a new session ID for security
session_start();
session_regenerate_id(true);

// Redirect to login page
header("Location: " . url_for('/login.php'));
exit;
