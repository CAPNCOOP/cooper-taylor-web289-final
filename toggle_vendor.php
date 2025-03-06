<?php
require_once 'private/initialize.php';

// Allow only Admin (3) and Super Admin (4)
if (!isset($_SESSION['user_id']) || ($_SESSION['user_level_id'] != 3 && $_SESSION['user_level_id'] != 4)) {
  header("Location: login.php");
  exit();
}

if (!isset($_GET['id'], $_GET['action'])) {
  $redirect_page = ($_SESSION['user_level_id'] == 4) ? "superadmin_dash.php" : "admin_dash.php";
  header("Location: $redirect_page?error=missing_parameters");
  exit();
}

$vendor_id = intval($_GET['id']);
$action = $_GET['action'];

try {
  // Verify vendor exists
  $sql = "SELECT vendor_id, vendor_status FROM vendor WHERE vendor_id = ?";
  $stmt = $db->prepare($sql);
  $stmt->execute([$vendor_id]);
  $vendor = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$vendor) {
    header("Location: superadmin_dash.php?error=vendor_not_found");
    exit();
  }

  // Determine new status
  $new_status = ($action === "activate") ? 'active' : 'inactive';

  // Update the vendor_status
  $sql = "UPDATE vendor SET vendor_status = ? WHERE vendor_id = ?";
  $stmt = $db->prepare($sql);
  $stmt->execute([$new_status, $vendor_id]);

  // Redirect back with success message
  $redirect_page = ($_SESSION['user_level_id'] == 4) ? "superadmin_dash.php" : "admin_dash.php";
  header("Location: $redirect_page?message=Vendor status updated successfully");
  exit();
} catch (PDOException $e) {
  header("Location: superadmin_dash.php?error=" . urlencode("Database error: " . $e->getMessage()));
  exit();
}
