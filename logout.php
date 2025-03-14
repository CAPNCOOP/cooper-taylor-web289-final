<?php
require_once 'private/initialize.php';

// Store username before destroying session
$goodbye_name = $_SESSION['username'] ?? '';

// Unset all session variables
session_unset();

// Destroy session data
session_destroy();

// Redirect to login page with goodbye message
header("Location: " . url_for('/login.php?message=logout_success&name=' . urlencode($goodbye_name)));
exit;
