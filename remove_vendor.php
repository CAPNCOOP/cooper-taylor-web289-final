<?php
require_once 'private/initialize.php';
require_login();

if (!isset($_GET['vendor_id'])) {
  header("Location: dashboard.php?message=error_invalid_vendor");
  exit();
}

$vendor_id = $_GET['vendor_id'];

// Check if the vendor is in the user's favorites before attempting to delete
$sql = "SELECT * FROM favorite WHERE user_id = ? AND vendor_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$_SESSION['user_id'], $vendor_id]);

if ($stmt->rowCount() > 0) {
  // Remove the vendor from the favorite table
  $sql = "DELETE FROM favorite WHERE user_id = ? AND vendor_id = ?";
  $stmt = $db->prepare($sql);
  if ($stmt->execute([$_SESSION['user_id'], $vendor_id])) {
    header("Location: dashboard.php?message=favorite_removed");
    exit();
  } else {
    header("Location: dashboard.php?message=error_remove_failed");
    exit();
  }
} else {
  header("Location: dashboard.php?message=error_invalid_vendor");
  exit();
}
