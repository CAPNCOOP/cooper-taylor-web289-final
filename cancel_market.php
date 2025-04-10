<?php
require_once 'private/initialize.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['week_id'])) {
  $week_id = (int) $_POST['week_id'];

  $admin = new Admin();
  $success = $admin->cancelMarketWeek($week_id);

  $_SESSION['message'] = $success
    ? "❌ Market cancelled."
    : "❌ Failed to cancel market.";
}

$redirect_page = ($_SESSION['user_level_id'] == 4) ? "superadmin_dash.php" : "admin_dash.php";
header("Location: $redirect_page");
exit();
