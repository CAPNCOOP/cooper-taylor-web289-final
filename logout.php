<?php
require_once 'private/initialize.php';

// Unset all session variables
session_unset();

// Destroy session data
session_destroy();

// Redirect to login page
header("Location: " . url_for('/login.php'));
exit;
