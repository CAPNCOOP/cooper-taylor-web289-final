<?php
require_once 'private/initialize.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['week_id'], $_POST['new_status'])) {
  $week_id = $_POST['week_id'];
  $new_status = $_POST['new_status'];

  if (!in_array($new_status, ['confirmed', 'cancelled'])) {
    $_SESSION['message'] = "❌ Invalid status.";
    header("Location: " . ($_SESSION['user_level_id'] == 3 ? "admin_dash.php" : "superadmin_dash.php"));
    exit();
  }

  $stmt = $db->prepare("UPDATE market_week SET market_status = ? WHERE week_id = ?");
  $stmt->execute([$new_status, $week_id]);

  $_SESSION['message'] = ($new_status === 'confirmed')
    ? "✅ Market has been reconfirmed."
    : "❌ Market has been cancelled.";
}

header("Location: " . ($_SESSION['user_level_id'] == 3 ? "admin_dash.php" : "superadmin_dash.php"));
exit();
