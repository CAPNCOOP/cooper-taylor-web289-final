<?php
$page_title = "Super Admin Dashboard";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_once 'private/functions.php';
require_once 'private/config.php';
Session::require_superadmin();

// fetch users, vendors, and admins
$superAdmin = new SuperAdmin();

$users = $superAdmin->fetchUsers();
$vendor_list = $superAdmin->fetchVendors();
$admins = $superAdmin->fetchAdmins();

$vendor_rsvp_map = $superAdmin->fetchVendorRsvps();
$market_weeks_map = $superAdmin->fetchMarketWeeksByVendor();
$upcoming_markets = $superAdmin->fetchUpcomingMarkets();
$homepage_content = $superAdmin->fetchHomepageContent();
$pending_vendors = $superAdmin->fetchPendingVendors();


?>


<main role="main">
  <h2>Welcome, Super Admin</h2>
  <?php require_once 'private/popup_message.php'; ?>
  <?php require_once 'admin_dash_sections.php'; ?>
</main>

<?php require_once 'private/footer.php'; ?>
