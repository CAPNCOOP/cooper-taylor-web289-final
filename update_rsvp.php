<?php
require_once 'private/initialize.php';

// Enable error reporting (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $vendor_id = $_POST['vendor_id'] ?? null;
  $week_id = $_POST['week_id'] ?? null;
  $status = $_POST['status'] ?? null;

  if (!$vendor_id || !$week_id || !$status) {
    header("Location: superadmin_dash.php?message=" . urlencode("❌ Missing vendor_id, week_id, or status."));
    exit();
  }

  try {
    // Update RSVP status in the database
    $sql = "UPDATE vendor_market SET status = ? WHERE vendor_id = ? AND week_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$status, $vendor_id, $week_id]);

    header("Location: superadmin_dash.php?message=" . urlencode("✅ RSVP updated to: $status"));
    exit();
  } catch (PDOException $e) {
    header("Location: superadmin_dash.php?message=" . urlencode("❌ Database error: " . $e->getMessage()));
    exit();
  }
} else {
  header("Location: superadmin_dash.php?message=" . urlencode("❌ Invalid request method."));
  exit();
}
