<?php
require_once 'private/initialize.php';
require_login();

if (!Session::is_vendor()) {
  exit("❌ Unauthorized access.");
}

$vendor = Vendor::find_by_user_id(Session::user_id());
if (!$vendor || $vendor->vendor_status !== 'approved') {
  exit("❌ ERROR: You must be an approved vendor to RSVP.");
}

$vendor_id = $vendor->vendor_id;

// Validate POST data
$week_id = $_POST['week_id'] ?? null;
$status = $_POST['status'] ?? null;

if (!$week_id || !$status) {
  exit("❌ ERROR: Missing market week or RSVP status.");
}

$db = DatabaseObject::getDatabase();

// Use Admin helper method to insert or update RSVP
Admin::saveVendorRsvp($vendor_id, $week_id, $status);

// Redirect with message
switch ($status) {
  case 'confirmed':
    $message = "✅ Market confirmed!";
    break;
  case 'canceled':
    $message = "❌ Market canceled.";
    break;
  case 'planned':
    $message = "📝 RSVP marked as planned.";
    break;
  default:
    $message = "🔄 RSVP updated.";
}
$session->message($message);
redirect_to("rsvp_market.php");
