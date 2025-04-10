<?php
require_once 'private/initialize.php';

// Auth check
if (!isset($_SESSION['user_id']) || ($_SESSION['user_level_id'] != 3 && $_SESSION['user_level_id'] != 4)) {
  header("Location: login.php");
  exit();
}

// Process the action
if (isset($_GET['vendor_id'], $_GET['action'])) {
  $vendor_id = (int) $_GET['vendor_id'];
  $action = $_GET['action'];

  $vendor = Vendor::find_by_id($vendor_id);

  if ($vendor) {
    $success = match ($action) {
      'approve' => $vendor->approve(),
      'reject'  => $vendor->reject(),
      default   => false
    };

    $_SESSION['message'] = $success
      ? "✅ Vendor status updated successfully!"
      : "❌ Invalid action or error updating status.";
  } else {
    $_SESSION['message'] = "❌ Vendor not found.";
  }
}

// Redirect based on user level
$redirect_page = ($_SESSION['user_level_id'] == 4) ? "superadmin_dash.php" : "admin_dash.php";
header("Location: $redirect_page");
exit();
