<?php
require_once 'private/initialize.php';
require_login();

$session->clear_message(); // Optional: clear old

if (!isset($_GET['vendor_id']) || !is_numeric($_GET['vendor_id'])) {
  $session->message("❌ Invalid vendor ID.");
  redirect_to('dashboard.php');
}

$vendor_id = (int) $_GET['vendor_id'];
$currentUser = User::find_by_id($_SESSION['user_id']);

if ($currentUser && Favorite::remove((int)$currentUser->user_id, (int)$vendor_id)) {
  $session->message("✅ Vendor removed successfully.");
} else {
  $session->message("❌ Vendor not found in your favorites.");
}

redirect_to('dashboard.php');
