<?php
require_once 'private/initialize.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_level_id'] < 3) {
  $_SESSION['message'] = "❌ Unauthorized access.";
  header("Location: login.php");
  exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $week_start = $_POST['week_start'] ?? '';
  $week_end = $_POST['week_end'] ?? '';
  $confirmation_deadline = $_POST['confirmation_deadline'] ?? '';

  $admin = new Admin();
  $message = $admin->createMarketWeek($week_start, $week_end, $confirmation_deadline);

  $_SESSION['message'] = $message;

  $redirect = ($_SESSION['user_level_id'] == 3) ? "admin_dash.php" : "superadmin_dash.php";
  header("Location: $redirect");
  exit();
}
