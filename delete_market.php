<?php
require_once 'private/initialize.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['week_id'])) {
  $week_id = $_POST['week_id'];

  $stmt = $db->prepare("UPDATE market_week SET is_deleted = 1 WHERE week_id = ?");
  $stmt->execute([$week_id]);

  $_SESSION['message'] = "🗑️ Market archived (soft deleted).";
}

header("Location: " . ($_SESSION['user_level_id'] == 3 ? "admin_dash.php" : "superadmin_dash.php"));
exit();
