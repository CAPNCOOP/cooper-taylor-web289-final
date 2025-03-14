<?php
require_once 'private/initialize.php';

// Ensure vendor is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php?message=error_not_logged_in");
  exit;
}

// Fetch vendor ID from the session
$user_id = $_SESSION['user_id'];
$sql = "SELECT vendor_id FROM vendor WHERE user_id = ? AND vendor_status = 'approved'";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id]);
$vendor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vendor) {
  header("Location: rsvp_market.php?message=error_vendor_not_approved");
  exit;
}

$vendor_id = $vendor['vendor_id'];

// Validate POST data
if (!isset($_POST['week_id']) || !isset($_POST['status'])) {
  header("Location: rsvp_market.php?message=error_missing_fields");
  exit;
}

$week_id = h($_POST['week_id']);
$status = h($_POST['status']);

// Check if RSVP already exists
$sql = "SELECT * FROM vendor_market WHERE vendor_id = ? AND week_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$vendor_id, $week_id]);
$existing_rsvp = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing_rsvp) {
  // Update existing RSVP
  $sql = "UPDATE vendor_market SET status = ? WHERE vendor_id = ? AND week_id = ?";
  $stmt = $db->prepare($sql);
  $stmt->execute([$status, $vendor_id, $week_id]);

  header("Location: rsvp_market.php?message=rsvp_updated");
  exit;
} else {
  // Insert new RSVP
  $sql = "INSERT INTO vendor_market (vendor_id, week_id, status) VALUES (?, ?, ?)";
  $stmt = $db->prepare($sql);
  $stmt->execute([$vendor_id, $week_id, $status]);

  header("Location: rsvp_market.php?message=rsvp_submitted");
  exit;
}
