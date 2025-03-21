<?php
require_once 'private/initialize.php';
require_login();

// Ensure the vendor_id is passed in the query string
if (isset($_GET['vendor_id'])) {
  $vendor_id = $_GET['vendor_id'];

  // Check if the vendor is in the user's favorites before attempting to delete
  $sql = "SELECT * FROM favorite WHERE user_id = ? AND vendor_id = ?";
  $stmt = $db->prepare($sql);
  $stmt->execute([$_SESSION['user_id'], $vendor_id]);

  if ($stmt->rowCount() > 0) {
    // Remove the vendor from the favorite table
    $sql = "DELETE FROM favorite WHERE user_id = ? AND vendor_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$_SESSION['user_id'], $vendor_id]);

    // Redirect back to the dashboard with a success message
    $_SESSION['message'] = "Vendor removed successfully.";
  } else {
    // If vendor is not in the favorites list
    $_SESSION['message'] = "Vendor not found in your saved vendors.";
  }
} else {
  $_SESSION['message'] = "Invalid vendor ID.";
}

header("Location: dashboard.php"); // Redirect back to the member dashboard
exit();
