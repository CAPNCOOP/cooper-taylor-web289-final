<?php
require_once 'private/initialize.php';

// Get username before destroying session
if (isset($_SESSION['username'])) {
  $goodbye_name = htmlspecialchars($_SESSION['username']);
} else {
  $goodbye_name = "User";
}

// Unset all session variables
session_unset();

// Destroy session data
session_destroy();

// Redirect to login page with logout message
header("Location: " . url_for('/login.php?logout_message=' . urlencode("✅ Goodbye, $goodbye_name! You have been logged out.")));
exit;
