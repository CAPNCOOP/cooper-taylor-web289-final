<?php
$page_title = "Admin Dashboard";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_once 'private/functions.php';

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['user_level_id'] != 3) { // 4 = Super Admin
  header("Location: login.php");
  exit();
}

$admin = new Admin();

$users = $admin->fetchUsers();
$vendor_list = $admin->fetchVendors();
$vendor_rsvp_map = $admin->fetchVendorRsvps();
$market_weeks_map = $admin->fetchMarketWeeksByVendor();
$upcoming_markets = $admin->fetchUpcomingMarkets();
$homepage_content = $admin->fetchHomepageContent();
$pending_vendors = $admin->fetchPendingVendors();

?>

<main>
  <h2>Welcome, Admin</h2>
  <?php require_once 'private/popup_message.php'; ?>
  <?php require_once 'admin_dash_sections.php'; ?>
</main>
<?php require_once 'private/footer.php'; ?>
