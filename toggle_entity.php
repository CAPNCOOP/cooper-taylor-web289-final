<?php
require_once 'private/initialize.php';

// Ensure only Admins (3) and Super Admins (4) can access
if (!isset($_SESSION['user_id']) || ($_SESSION['user_level_id'] != 3 && $_SESSION['user_level_id'] != 4)) {
  $_SESSION['message'] = "❌ Unauthorized access.";
  header("Location: login.php");
  exit();
}

// Validate parameters
if (!isset($_GET['id'], $_GET['action'], $_GET['type'])) {
  $_SESSION['message'] = "❌ Missing parameters.";
  header("Location: superadmin_dash.php");
  exit();
}

$entity_id = (int) $_GET['id'];
$type = $_GET['type'];
$redirect_page = ($_SESSION['user_level_id'] == 4) ? "superadmin_dash.php" : "admin_dash.php";

try {
  $admin = new Admin();
  $success = false;

  switch ($type) {
    case 'user':
    case 'admin':
      $success = $admin->toggleUserStatus($entity_id);
      break;

    case 'vendor':
      $success = $admin->toggleVendorStatus($entity_id);
      break;

    default:
      $_SESSION['message'] = "❌ Invalid entity type.";
      header("Location: $redirect_page");
      exit();
  }

  if ($success) {
    $_SESSION['message'] = "✅ {$type} status toggled successfully.";
  } else {
    $_SESSION['message'] = "❌ Error toggling {$type} status.";
  }
} catch (Exception $e) {
  $_SESSION['message'] = "❌ Exception: " . $e->getMessage();
}

header("Location: $redirect_page");
exit();
