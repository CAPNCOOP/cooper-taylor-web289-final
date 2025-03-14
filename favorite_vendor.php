<?php
require_once 'private/initialize.php';
require_login();

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['success' => false, 'message' => 'error_not_logged_in']);
  exit;
}

$user_id = $_SESSION['user_id'];
$vendor_id = isset($_POST['vendor_id']) ? (int) $_POST['vendor_id'] : 0;

if ($vendor_id <= 0) {
  echo json_encode(['success' => false, 'message' => 'error_invalid_vendor']);
  exit;
}

// ✅ Improved query using EXISTS() instead of fetching the entire row
$sql = "SELECT EXISTS(SELECT 1 FROM favorite WHERE user_id = ? AND vendor_id = ?) AS exists_flag";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id, $vendor_id]);
$favorite_exists = $stmt->fetchColumn();

if ($favorite_exists) {
  // ✅ Vendor is already in favorites → Remove it
  $sql = "DELETE FROM favorite WHERE user_id = ? AND vendor_id = ?";
  $stmt = $db->prepare($sql);
  if ($stmt->execute([$user_id, $vendor_id])) {
    echo json_encode(['success' => true, 'message' => 'favorite_removed']);
  } else {
    error_log("❌ Failed to remove vendor [$vendor_id] from user [$user_id] favorites.");
    echo json_encode(['success' => false, 'message' => 'error_remove_failed']);
  }
} else {
  // ✅ Vendor is NOT in favorites → Add it
  $sql = "INSERT INTO favorite (user_id, vendor_id) VALUES (?, ?)";
  $stmt = $db->prepare($sql);
  if ($stmt->execute([$user_id, $vendor_id])) {
    echo json_encode(['success' => true, 'message' => 'favorite_added']);
  } else {
    error_log("❌ Failed to add vendor [$vendor_id] to user [$user_id] favorites.");
    echo json_encode(['success' => false, 'message' => 'error_add_failed']);
  }
}

exit;
