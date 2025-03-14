<?php
require_once 'private/initialize.php';

// Ensure only Admins (3) and Super Admins (4) can access
if (!isset($_SESSION['user_id']) || ($_SESSION['user_level_id'] != 3 && $_SESSION['user_level_id'] != 4)) {
  $_SESSION['message'] = "❌ Unauthorized access.";
  header("Location: login.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Sanitize and validate input
  $vendor_id = isset($_POST['vendor_id']) ? filter_var($_POST['vendor_id'], FILTER_VALIDATE_INT) : null;
  $week_id = isset($_POST['week_id']) ? filter_var($_POST['week_id'], FILTER_VALIDATE_INT) : null;
  $status = isset($_POST['status']) ? trim($_POST['status']) : null;

  // Ensure all required fields are present
  if (!$vendor_id || !$week_id || !$status || !in_array($status, ['planned', 'confirmed', 'canceled'])) {
    $_SESSION['message'] = "❌ Invalid input. Ensure all fields are filled correctly.";
    header("Location: " . ($_SESSION['user_level_id'] == 4 ? "superadmin_dash.php" : "admin_dash.php"));
    exit();
  }

  try {
    // Update RSVP status in the database
    $sql = "UPDATE vendor_market SET status = ? WHERE vendor_id = ? AND week_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$status, $vendor_id, $week_id]);

    // Set success message
    $_SESSION['message'] = "✅ RSVP updated to: " . ucfirst($status);
  } catch (PDOException $e) {
    error_log("Database error in update_rsvp.php: " . $e->getMessage());
    $_SESSION['message'] = "❌ Database error: Unable to update RSVP.";
  }

  // Redirect back to dashboard
  header("Location: " . ($_SESSION['user_level_id'] == 4 ? "superadmin_dash.php" : "admin_dash.php"));
  exit();
} else {
  $_SESSION['message'] = "❌ Invalid request method.";
  header("Location: " . ($_SESSION['user_level_id'] == 4 ? "superadmin_dash.php" : "admin_dash.php"));
  exit();
}
