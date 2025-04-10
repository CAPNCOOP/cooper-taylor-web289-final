<?php
require_once 'private/initialize.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $section = $_POST['section'] ?? '';
  $content = $_POST['content'] ?? '';

  $admin = new Admin();
  $success = $admin->updateHomepageContent($section, $content);

  $_SESSION['message'] = $success
    ? "✅ Homepage content updated."
    : "❌ Failed to update homepage content.";

  $redirect = ($_SESSION['user_level_id'] == 4) ? "superadmin_dash.php" : "admin_dash.php";
  header("Location: $redirect");
  exit();
}
