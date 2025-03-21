<?php
require_once 'private/initialize.php';

// Ensure vendor is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit("Unauthorized access.");
}

// Fetch vendor ID from the session
$user_id = $_SESSION['user_id'];
$sql = "SELECT vendor_id FROM vendor WHERE user_id = ? AND vendor_status = 'approved'";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id]);
$vendor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vendor) {
  exit("❌ ERROR: You must be an approved vendor to RSVP.");
}

$vendor_id = $vendor['vendor_id'];

// Validate POST data
if (!isset($_POST['week_id']) || !isset($_POST['status'])) {
  exit("❌ ERROR: Missing market week or RSVP status.");
}

$week_id = $_POST['week_id'];
$status = $_POST['status'];

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

  $message = "RSVP updated successfully!";
} else {
  // Insert new RSVP
  $sql = "INSERT INTO vendor_market (vendor_id, week_id, status) VALUES (?, ?, ?)";
  $stmt = $db->prepare($sql);
  $stmt->execute([$vendor_id, $week_id, $status]);

  $message = "RSVP submitted successfully!";
}

// Redirect back to RSVP page with success message
header("Location: rsvp_market.php?message=" . urlencode($message));
exit;
