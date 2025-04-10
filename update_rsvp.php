<?php
require_once 'private/initialize.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $vendor_id = filter_input(INPUT_POST, 'vendor_id', FILTER_VALIDATE_INT);
  $week_id = filter_input(INPUT_POST, 'week_id', FILTER_VALIDATE_INT);
  $status = isset($_POST['status']) ? trim($_POST['status']) : null;

  $redirect = ($_SESSION['user_level_id'] == 4) ? "superadmin_dash.php" : "admin_dash.php";

  if (!$vendor_id || !$week_id || !$status) {
    $_SESSION['message'] = "❌ Missing vendor_id, week_id, or status.";
    header("Location: $redirect");
    exit();
  }

  $admin = new Admin();
  $success = $admin->updateVendorRsvpStatus($vendor_id, $week_id, $status);

  $_SESSION['message'] = $success
    ? "✅ RSVP updated to: $status"
    : "❌ Failed to update RSVP.";

  header("Location: $redirect");
  exit();
}

$_SESSION['message'] = "❌ Invalid request method.";
header("Location: " . ($_SESSION['user_level_id'] == 4 ? "superadmin_dash.php" : "admin_dash.php"));
exit();
