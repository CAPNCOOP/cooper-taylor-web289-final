<?php
// require_once 'private/initialize.php';
// require_login();

// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vendor_id'])) {
//   $user_id = $_SESSION['user_id'];
//   $vendor_id = $_POST['vendor_id'];

//   // Check if the vendor is already favorited
//   $sql = "SELECT * FROM favorite WHERE user_id = ? AND vendor_id = ?";
//   $stmt = $db->prepare($sql);
//   $stmt->execute([$user_id, $vendor_id]);

//   if ($stmt->fetch()) {
//     // If already favorited, remove it
//     $sql = "DELETE FROM favorite WHERE user_id = ? AND vendor_id = ?";
//     $stmt = $db->prepare($sql);
//     $stmt->execute([$user_id, $vendor_id]);
//     echo "Removed from favorite.";
//   } else {
//     // Otherwise, add to favorite
//     $sql = "INSERT INTO favorite (user_id, vendor_id) VALUES (?, ?)";
//     $stmt = $db->prepare($sql);
//     $stmt->execute([$user_id, $vendor_id]);
//     echo "Added to favorite.";
//   }
// }

// require_once 'private/initialize.php';
// require_login();

// // Check if vendor_id is set
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vendor_id'])) {
//   $user_id = $_SESSION['user_id'];
//   $vendor_id = $_POST['vendor_id'];

//   // Check if vendor is already favorited
//   $sql = "SELECT * FROM favorite WHERE user_id = ? AND vendor_id = ?";
//   $stmt = $db->prepare($sql);
//   $stmt->execute([$user_id, $vendor_id]);

//   if ($stmt->fetch()) {
//     // If already favorited, remove it
//     $sql = "DELETE FROM favorite WHERE user_id = ? AND vendor_id = ?";
//     $stmt = $db->prepare($sql);
//     $stmt->execute([$user_id, $vendor_id]);

//     $message = "Removed from favorite.";
//   } else {
//     // Otherwise, add to favorite
//     $sql = "INSERT INTO favorite (user_id, vendor_id) VALUES (?, ?)";
//     $stmt = $db->prepare($sql);
//     $stmt->execute([$user_id, $vendor_id]);

//     $message = "Added to favorite.";
//   }

//   // ✅ Handle JSON for JavaScript-based requests
//   if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
//     echo json_encode(["status" => "success", "message" => $message]);
//     exit;
//   }

//   // ✅ PHP Fallback - Redirect if JavaScript is disabled
//   header("Location: dashboard.php?message=" . urlencode($message));
//   exit;
// 
require_once 'private/initialize.php';
require_login();

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['success' => false, 'message' => 'User not logged in']);
  exit;
}

$user_id = $_SESSION['user_id'];
$vendor_id = $_POST['vendor_id'] ?? 0;

if (!$vendor_id) {
  echo json_encode(['success' => false, 'message' => 'Invalid vendor']);
  exit;
}

// Check if vendor is already in favorites
$sql = "SELECT * FROM favorite WHERE user_id = ? AND vendor_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id, $vendor_id]);
$favorite = $stmt->fetch(PDO::FETCH_ASSOC);

if ($favorite) {
  // Vendor is already in favorites → Remove it
  $sql = "DELETE FROM favorite WHERE user_id = ? AND vendor_id = ?";
  $stmt = $db->prepare($sql);
  if ($stmt->execute([$user_id, $vendor_id])) {
    echo json_encode(['success' => true, 'message' => 'Vendor removed from favorites']);
  } else {
    echo json_encode(['success' => false, 'message' => 'Database error while removing']);
  }
} else {
  // Vendor is NOT in favorites → Add it
  $sql = "INSERT INTO favorite (user_id, vendor_id) VALUES (?, ?)";
  $stmt = $db->prepare($sql);
  if ($stmt->execute([$user_id, $vendor_id])) {
    echo json_encode(['success' => true, 'message' => 'Vendor added to favorites']);
  } else {
    echo json_encode(['success' => false, 'message' => 'Database error while adding']);
  }
}
exit;
