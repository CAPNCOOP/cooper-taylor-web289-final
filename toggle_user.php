<?php
require_once 'private/initialize.php';

// Restrict access to admins
if (!isset($_SESSION['user_id']) || $_SESSION['user_level_id'] != 3) {
  header("Location: login.php");
  exit();
}

if (isset($_GET['id']) && isset($_GET['action'])) {
  $user_id = intval($_GET['id']);
  $action = $_GET['action'];

  // Get target user's role
  $sql = "SELECT user_level_id FROM users WHERE user_id = ?";
  $stmt = $db->prepare($sql);
  $stmt->execute([$user_id]);
  $target_user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$target_user) {
    header("Location: admin_dash.php?error=user_not_found");
    exit();
  }

  // 🚨 Prevent Admins (level 3) from deactivating other Admins
  if ($_SESSION['user_level_id'] == 3 && $target_user['user_level_id'] == 3) {
    header("Location: admin_dash.php?error=no_permission");
    exit();
  }

  if ($action === "activate") {
    $sql = "UPDATE users SET is_active = 1 WHERE user_id = ?";
  } elseif ($action === "deactivate") {
    $sql = "UPDATE users SET is_active = 0 WHERE user_id = ?";
  } else {
    header("Location: admin_dash.php");
    exit();
  }

  $stmt = $db->prepare($sql);
  $stmt->execute([$user_id]);
}

header("Location: admin_dash.php");
exit();
