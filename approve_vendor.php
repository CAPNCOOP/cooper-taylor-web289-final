<?php
require_once 'private/initialize.php';

// Ensure only Admins & Super Admins can access
if (!isset($_SESSION['user_id']) || ($_SESSION['user_level_id'] != 3 && $_SESSION['user_level_id'] != 4)) {
  header("Location: login.php");
  exit;
}

// Validate vendor_id and action
if (isset($_GET['vendor_id']) && isset($_GET['action'])) {
  $vendor_id = intval($_GET['vendor_id']);
  $action = $_GET['action'];

  if ($action === "approve") {
    $sql = "UPDATE vendor SET vendor_status = ? WHERE vendor_id = ?";
    $params = ['approved', $vendor_id];
    $message = 'vendor_approved';
  } elseif ($action === "reject") {
    $sql = "UPDATE vendor SET vendor_status = ? WHERE vendor_id = ?";
    $params = ['denied', $vendor_id];
    $message = 'vendor_denied';
  } else {
    header("Location: admin_dash.php?message=error_invalid_action");
    exit;
  }

  $stmt = $db->prepare($sql);
  if ($stmt->execute($params)) {
    header("Location: admin_dash.php?message=$message");
  } else {
    header("Location: admin_dash.php?message=error_update_failed");
  }
  exit;
}
