<?php
require_once 'private/initialize.php';
require_login();

header('Content-Type: application/json');

if (!$session->is_logged_in()) {
  echo json_encode(['success' => false, 'message' => 'error_not_logged_in']);
  exit;
}

$user_id = $_SESSION['user_id'] ?? 0;
$vendor_id = $_POST['vendor_id'] ?? 0;

if (!$vendor_id) {
  echo json_encode(['success' => false, 'message' => 'error_invalid_vendor']);
  exit;
}

$success = Favorite::toggle($user_id, $vendor_id);
$message = $success === true ? 'favorite_added' : ($success === false ? 'favorite_removed' : 'error_failed');

echo json_encode([
  'success' => $success !== null,
  'message' => $message
]);

exit;
