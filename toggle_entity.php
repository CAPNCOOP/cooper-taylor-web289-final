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
      $table = "users"; // Vendor activation happens in users table
      $id_column = "user_id"; // Vendors are users
      $status_column = "is_active"; // Toggle is_active instead of vendor_status
      $redirect_page = ($_SESSION['user_level_id'] == 4) ? "superadmin_dash.php" : "admin_dash.php";
      break;

    default:
      $_SESSION['message'] = "❌ Invalid entity type.";
      header("Location: superadmin_dash.php");
      exit();
  }


  // Verify entity exists
  $sql = "SELECT $id_column FROM $table WHERE $id_column = ?";
  $stmt = $db->prepare($sql);
  $stmt->execute([$entity_id]);
  $entity = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$entity) {
    $_SESSION['message'] = "❌ " . ucfirst($type) . " not found.";
    header("Location: $redirect_page");
    exit();
  }

  // Determine new status
  if ($type === "vendor") {
    $new_status = ($action === "activate") ? 'approved' : 'inactive'; // Vendors should be 'approved' or 'inactive'
    $status_column = "vendor_status"; // Vendor status is in the vendor table
  } else {
    $new_status = ($action === "activate") ? 1 : 0; // Regular users & admins should be 1 or 0
    $status_column = "is_active";
  }

  // Update entity status
  $sql = "UPDATE $table SET $status_column = ? WHERE $id_column = ?";
  $stmt = $db->prepare($sql);
  $stmt->execute([$new_status, $entity_id]);

  // Set success message
  $_SESSION['message'] = "✅ " . ucfirst($type) . " " . ($new_status === 'active' || $new_status === 1 ? "activated!" : "deactivated.");
} catch (PDOException $e) {
  $_SESSION['message'] = "❌ Database error: " . $e->getMessage();
}

// Redirect back
header("Location: $redirect_page");
exit();
