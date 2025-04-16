<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$friendly_names = [
  'index.php' => 'Home',
  'aboutus.php' => 'About Us',
  'ourvendors.php' => 'Our Vendors',
  'vendor_dash.php' => 'Vendor Dashboard',
  'admin_dash.php' => 'Admin Dashboard',
  'superadmin_dash.php' => 'Super Admin Dashboard',
  'schedule.php' => 'Market Schedule',
];

$current_page = basename($_SERVER['SCRIPT_NAME']);
$current_label = $friendly_names[$current_page] ?? ucwords(str_replace(['_', '.php'], [' ', ''], $current_page));

// Don't breadcrumb these pages
$excluded_pages = ['login.php', 'signup.php', 'vendorsignup.php', 'logout.php', '404.php'];
if (in_array($current_page, $excluded_pages)) return;

// Reset on login
if (!isset($_SESSION['breadcrumbs']) || !is_array($_SESSION['breadcrumbs']) || count($_SESSION['breadcrumbs']) < 1) {
  $_SESSION['breadcrumbs'] = [['label' => 'Home', 'path' => 'index.php']];
}

// Only add if not already last
$last = end($_SESSION['breadcrumbs']);
if (!isset($last['path']) || $last['path'] !== $current_page) {
  $_SESSION['breadcrumbs'][] = ['label' => $current_label, 'path' => $current_page];
}

// Keep it at 2 items: Home + Current
if (count($_SESSION['breadcrumbs']) > 2) {
  array_shift($_SESSION['breadcrumbs']);
}
