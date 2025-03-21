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

$entity_id = intval($_GET['id']);
$action = $_GET['action'];
$type = $_GET['type']; // "user", "vendor", "admin"

try {
  // Determine table, ID column, and status column dynamically
  switch ($type) {
    case "user":
    case "admin":
      $table = "users";
      $id_column = "user_id";
      $status_column = "is_active";
      $redirect_page = ($_SESSION['user_level_id'] == 4) ? "superadmin_dash.php" : "admin_dash.php";
      break;

    case "vendor":
      $table = "users"; // ✅ Vendors should be toggled in `users` table
      $id_column = "user_id"; // ✅ Vendors are users, toggle their `user_id`
      $status_column = "is_active"; // ✅ Change activation (1 = active, 0 = inactive)
      $redirect_page = ($_SESSION['user_level_id'] == 4) ? "superadmin_dash.php" : "admin_dash.php";
      break;

    default:
      $_SESSION['message'] = "❌ Invalid entity type.";
      header("Location: superadmin_dash.php");
      exit();
  }

  // ✅ Fetch current status
  $sql = "SELECT $status_column FROM $table WHERE $id_column = ?";
  $stmt = $db->prepare($sql);
  $stmt->execute([$entity_id]);
  $current_status = $stmt->fetchColumn();

  if ($current_status === false) {
    $_SESSION['message'] = "❌ " . ucfirst($type) . " not found.";
    header("Location: $redirect_page");
    exit();
  }

  // ✅ Determine new status (ONLY 1 or 0 for activation)
  $new_status = ($current_status == 1) ? 0 : 1;

  // ✅ Update entity activation status (ONLY is_active, NOT vendor_status)
  $sql = "UPDATE $table SET $status_column = ? WHERE $id_column = ?";
  $stmt = $db->prepare($sql);
  $result = $stmt->execute([$new_status, $entity_id]);

  // ✅ Debug: Verify the update worked
  if ($result) {
    $_SESSION['message'] = "✅ " . ucfirst($type) . " status changed to: " . ($new_status ? "Active" : "Inactive");
  } else {
    $_SESSION['message'] = "❌ Error updating $type status: " . implode(" | ", $stmt->errorInfo());
  }
} catch (PDOException $e) {
  $_SESSION['message'] = "❌ Database error: " . $e->getMessage();
}

// Redirect back
header("Location: $redirect_page");
exit();
