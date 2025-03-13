<?php
require_once 'private/initialize.php';

// Ensure only Admins & Super Admins can access
if (!isset($_SESSION['user_id']) || ($_SESSION['user_level_id'] != 3 && $_SESSION['user_level_id'] != 4)) {
  header("Location: login.php");
  exit();
}

if (isset($_GET['vendor_id']) && isset($_GET['action'])) {
  $vendor_id = intval($_GET['vendor_id']);
  $action = $_GET['action'];

  if ($action === "approve") {
    $sql = "UPDATE vendor SET vendor_status = ? WHERE vendor_id = ?";
    $params = ['approved', $vendor_id];
  } elseif ($action === "reject") {
    $sql = "UPDATE vendor SET vendor_status = ? WHERE vendor_id = ?";
    $params = ['denied', $vendor_id];
  } else {
    $_SESSION['message'] = "❌ Invalid action.";
    header("Location: admin_dash.php");
    exit();
  }

  $stmt = $db->prepare($sql);
  if ($stmt->execute($params)) {
    $_SESSION['message'] = "✅ Vendor status updated successfully!";
  } else {
    $_SESSION['message'] = "❌ Error updating vendor status.";
  }
}

// Redirect back to the dashboard
$redirect_page = ($_SESSION['user_level_id'] == 4) ? "superadmin_dash.php" : "admin_dash.php";
header("Location: $redirect_page");
exit();
