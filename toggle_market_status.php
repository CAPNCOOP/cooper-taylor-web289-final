<?php
require_once 'private/initialize.php';
Session::require_login();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['week_id'], $_POST['new_status'])) {
  $week_id = $_POST['week_id'];
  $new_status = $_POST['new_status'];

  if (!in_array($new_status, ['confirmed', 'cancelled'])) {
    $session->message("❌ Invalid status.");
    redirect_to(Session::user_level_id() == 3 ? "admin_dash.php" : "superadmin_dash.php");
  }

  try {
    Admin::toggleMarketStatus($week_id, $new_status);

    $message = ($new_status === 'confirmed')
      ? "✅ Market has been reconfirmed."
      : "❌ Market has been cancelled.";
    $session->message($message);
  } catch (Exception $e) {
    $session->message("❌ Error updating market status: " . h($e->getMessage()));
  }
}

redirect_to(Session::user_level_id() == 3 ? "admin_dash.php" : "superadmin_dash.php");
