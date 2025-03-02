<?php
require_once 'private/initialize.php';

// Ensure only Admins/Super Admins can access
if (!isset($_SESSION['user_id']) || ($_SESSION['user_level_id'] < 3)) {
  header("Location: login.php");
  exit("Unauthorized access.");
}

// Validate form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $week_start = $_POST['week_start'];
  $week_end = $_POST['week_end'];
  $confirmation_deadline = $_POST['confirmation_deadline'];

  // Validate dates
  if ($week_start >= $week_end) {
    exit("❌ Week start must be before week end.");
  }
  if ($confirmation_deadline >= $week_start) {
    exit("❌ Confirmation deadline must be before the market starts.");
  }

  // Insert into market_week table
  $sql = "INSERT INTO market_week (market_id, week_start, week_end, confirmation_deadline) 
            VALUES (1, ?, ?, ?)"; // Assuming market_id is always 1
  $stmt = $db->prepare($sql);
  $stmt->execute([$week_start, $week_end, $confirmation_deadline]);

  // Redirect back to dashboard with success message
  header("Location: admin_dash.php?message=Market scheduled successfully!");
  exit;
}
