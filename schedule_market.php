<?php
require_once 'private/initialize.php';

// Ensure only Admins/Super Admins can access
if (!isset($_SESSION['user_id']) || ($_SESSION['user_level_id'] < 3)) {
  $_SESSION['message'] = "❌ Unauthorized access.";
  header("Location: login.php");
  exit();
}

// Validate form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $week_start = $_POST['week_start'];
  $week_end = $_POST['week_end'];
  $confirmation_deadline = $_POST['confirmation_deadline'];

  // Validate dates
  if ($week_start >= $week_end) {
    $_SESSION['message'] = "❌ Week start must be before week end.";
    header("Location: " . ($_SESSION['user_level_id'] == 3 ? "admin_dash.php" : "superadmin_dash.php"));
    exit();
  }
  if ($confirmation_deadline >= $week_start) {
    $_SESSION['message'] = "❌ Confirmation deadline must be before the market starts.";
    header("Location: " . ($_SESSION['user_level_id'] == 3 ? "admin_dash.php" : "superadmin_dash.php"));
    exit();
  }

  try {
    // Insert into market_week table
    $sql = "INSERT INTO market_week (market_id, week_start, week_end, confirmation_deadline) 
            VALUES (1, ?, ?, ?)"; // Assuming market_id is always 1
    $stmt = $db->prepare($sql);
    $stmt->execute([$week_start, $week_end, $confirmation_deadline]);

    $_SESSION['message'] = "✅ Market week scheduled successfully!";
  } catch (PDOException $e) {
    $_SESSION['message'] = "❌ Database error: " . $e->getMessage();
  }

  // Redirect back to the correct dashboard
  header("Location: " . ($_SESSION['user_level_id'] == 3 ? "admin_dash.php" : "superadmin_dash.php"));
  exit();
}
