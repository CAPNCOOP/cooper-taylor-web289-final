<?php
require_once 'private/initialize.php';

// Restrict to Admins and Super Admins
if (!isset($_SESSION['user_id']) || $_SESSION['user_level_id'] < 3) {
  $_SESSION['message'] = "❌ Unauthorized access.";
  header("Location: login.php");
  exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $week_start = $_POST['week_start'];
  $week_end = $_POST['week_end'];
  $confirmation_deadline = $_POST['confirmation_deadline'];
  $redirect = ($_SESSION['user_level_id'] == 3) ? "admin_dash.php" : "superadmin_dash.php";

  // Basic date validations
  if ($week_start >= $week_end) {
    $_SESSION['message'] = "❌ Week start must be before week end.";
    header("Location: $redirect");
    exit();
  }

  if ($confirmation_deadline >= $week_start) {
    $_SESSION['message'] = "❌ Deadline must be before week start.";
    header("Location: $redirect");
    exit();
  }

  try {
    // Check for existing entry
    $check = $db->prepare("SELECT COUNT(*) FROM market_week WHERE week_start = ?");
    $check->execute([$week_start]);

    if ($check->fetchColumn() > 0) {
      $_SESSION['message'] = "❌ A market is already scheduled for this date.";
    } else {
      // Insert new week
      $sql = "INSERT INTO market_week (week_start, week_end, market_status, confirmation_deadline)
              VALUES (?, ?, 'confirmed', ?)";
      $stmt = $db->prepare($sql);
      $stmt->execute([$week_start, $week_end, $confirmation_deadline]);

      $_SESSION['message'] = "✅ Market week scheduled!";
    }
  } catch (PDOException $e) {
    $_SESSION['message'] = "❌ Database error.";
  }

  header("Location: $redirect");
  exit();
}
