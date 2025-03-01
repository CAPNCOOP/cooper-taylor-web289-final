<?php
require_once 'private/initialize.php';

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Unset all session variables
session_unset();

// Destroy session data
session_destroy();

// Redirect to login page
header("Location: " . url_for('/login.php'));
exit;
