<?php
require_once 'private/initialize.php';

// Restrict access: Only allow Admins (3) and Super Admins (4)
if (!isset($_SESSION['user_id']) || ($_SESSION['user_level_id'] != 3 && $_SESSION['user_level_id'] != 4)) {
  header("Location: login.php");
  exit();
}

if (!isset($_GET['id']) || !isset($_GET['action'])) {
  $redirect_page = ($_SESSION['user_level_id'] == 4) ? "superadmin_dash.php" : "admin_dash.php";
  header("Location: $redirect_page?error=missing_parameters");
  exit();
}

$user_id = intval($_GET['id']);
$action = $_GET['action'];

try {
  // Fetch the target user's data
  $sql = "SELECT user_id, user_level_id, is_active FROM users WHERE user_id = ?";
  $stmt = $db->prepare($sql);
  $stmt->execute([$user_id]);
  $target_user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$target_user) {
    header("Location: superadmin_dash.php?error=user_not_found");
    exit();
  }

  // Toggle activation status
  $new_status = ($action === "activate") ? 1 : 0;

  // Update users table for normal users & admins
  $sql = "UPDATE users SET is_active = ? WHERE user_id = ?";
  $stmt = $db->prepare($sql);
  $stmt->execute([$new_status, $user_id]);

  // Redirect back
  $redirect_page = ($_SESSION['user_level_id'] == 4) ? "superadmin_dash.php" : "admin_dash.php";
  header("Location: $redirect_page?message=Status updated successfully");
  exit();
} catch (PDOException $e) {
  $redirect_page = ($_SESSION['user_level_id'] == 4) ? "superadmin_dash.php" : "admin_dash.php";
  header("Location: $redirect_page?error=" . urlencode("Database error: " . $e->getMessage()));
  exit();
}
