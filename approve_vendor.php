<?php
require_once 'private/initialize.php';
session_start();

// Ensure only Admins & Super Admins can access
if (!isset($_SESSION['user_id']) || ($_SESSION['user_level_id'] != 3 && $_SESSION['user_level_id'] != 4)) {
  header("Location: login.php");
  exit();
}

if (isset($_GET['vendor_id']) && isset($_GET['action'])) {
  $vendor_id = intval($_GET['vendor_id']);
  $action = $_GET['action'];

  if ($action === "approve") {
    $sql = "UPDATE vendor SET vendor_status = 'approved' WHERE vendor_id = ?";
  } elseif ($action === "reject") {
    $sql = "UPDATE vendor SET vendor_status = 'denied' WHERE vendor_id = ?";
  } elseif ($action === "reset") {
    $sql = "UPDATE vendor SET vendor_status = 'pending' WHERE vendor_id = ?";
  } else {
    header("Location: admin_dash.php"); // 🚀 Keep this so invalid actions don’t break things
    exit();
  }

  $stmt = $db->prepare($sql);
  $stmt->execute([$vendor_id]);
}

// Redirect back to the dashboard
header("Location: admin_dash.php");
exit();
