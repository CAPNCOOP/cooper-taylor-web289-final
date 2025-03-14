<?php
require_once 'private/initialize.php';

// Ensure only Admins/Super Admins can access
if (!isset($_SESSION['user_id']) || $_SESSION['user_level_id'] < 3) {
  header("Location: login.php?message=error_unauthorized");
  exit();
}

// Validate form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // CSRF Protection
  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header("Location: " . ($_SESSION['user_level_id'] == 3 ? "admin_dash.php" : "superadmin_dash.php") . "?message=error_csrf");
    exit();
  }

  // Sanitize inputs
  $week_start = htmlspecialchars($_POST['week_start']);
  $week_end = htmlspecialchars($_POST['week_end']);
  $confirmation_deadline = htmlspecialchars($_POST['confirmation_deadline']);

  // Validate dates
  if ($week_start >= $week_end) {
    header("Location: " . ($_SESSION['user_level_id'] == 3 ? "admin_dash.php" : "superadmin_dash.php") . "?message=error_invalid_dates");
    exit();
  }
  if ($confirmation_deadline >= $week_start) {
    header("Location: " . ($_SESSION['user_level_id'] == 3 ? "admin_dash.php" : "superadmin_dash.php") . "?message=error_confirmation_deadline");
    exit();
  }

  try {
    // Insert into market_week table
    $sql = "INSERT INTO market_week (market_id, week_start, week_end, confirmation_deadline) 
            VALUES (1, ?, ?, ?)"; // Assuming market_id is always 1
    $stmt = $db->prepare($sql);
    $stmt->execute([$week_start, $week_end, $confirmation_deadline]);

    header("Location: " . ($_SESSION['user_level_id'] == 3 ? "admin_dash.php" : "superadmin_dash.php") . "?message=market_scheduled");
    exit();
  } catch (PDOException $e) {
    header("Location: " . ($_SESSION['user_level_id'] == 3 ? "admin_dash.php" : "superadmin_dash.php") . "?message=error_database");
    exit();
  }
}
