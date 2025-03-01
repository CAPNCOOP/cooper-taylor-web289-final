<?php
require_once 'private/initialize.php';
session_start();

// Restrict access to super admins
if (!isset($_SESSION['user_id']) || $_SESSION['user_level_id'] != 4) {
  header("Location: login.php");
  exit();
}

if (isset($_GET['id']) && isset($_GET['action'])) {
  $user_id = intval($_GET['id']);
  $action = $_GET['action'];

  if ($action === "promote") {
    $sql = "UPDATE users SET user_level_id = 3 WHERE user_id = ?";
  } elseif ($action === "demote") {
    $sql = "UPDATE users SET user_level_id = 1 WHERE user_id = ?";
  } else {
    header("Location: superadmin_dash.php");
    exit();
  }

  $stmt = $db->prepare($sql);
  $stmt->execute([$user_id]);
}

header("Location: superadmin_dash.php");
exit();
