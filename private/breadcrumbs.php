<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$current_path = basename($_SERVER['PHP_SELF']);
$current_label = $page_title ?? ucfirst(pathinfo($current_path, PATHINFO_FILENAME));

$excluded_pages = ['login.php', 'vendorsignup.php', 'signup.php', 'logout.php'];

// Skip excluded pages
if (in_array($current_path, $excluded_pages)) {
  return;
}

// Clear trail if we're back at home
if ($current_path === 'index.php') {
  $_SESSION['breadcrumbs'] = [
    ['label' => 'Home', 'path' => 'index.php']
  ];
} else {
  // Init if not set
  if (!isset($_SESSION['breadcrumbs'])) {
    $_SESSION['breadcrumbs'] = [
      ['label' => 'Home', 'path' => 'index.php']
    ];
  }

  // Remove current page if it already exists
  $_SESSION['breadcrumbs'] = array_filter($_SESSION['breadcrumbs'], function ($crumb) use ($current_path) {
    return $crumb['path'] !== $current_path;
  });

  // Add the current page
  $_SESSION['breadcrumbs'][] = [
    'label' => $current_label,
    'path' => $current_path
  ];

  // Keep Home + 1 recent page
  $_SESSION['breadcrumbs'] = array_slice($_SESSION['breadcrumbs'], -2);
}
