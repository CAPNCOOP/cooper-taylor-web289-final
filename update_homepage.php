<?php
require_once 'private/initialize.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $section = $_POST['section'];
  $content = $_POST['content'];

  $stmt = $db->prepare("SELECT COUNT(*) FROM homepage_content WHERE section = ?");
  $stmt->execute([$section]);

  if ($stmt->fetchColumn() > 0) {
    $update = $db->prepare("UPDATE homepage_content SET content = ? WHERE section = ?");
    $update->execute([$content, $section]);
  } else {
    $insert = $db->prepare("INSERT INTO homepage_content (section, content) VALUES (?, ?)");
    $insert->execute([$section, $content]);
  }

  $_SESSION['message'] = "✅ Homepage content updated.";
  header("Location: " . ($_SESSION['user_level_id'] == 3 ? "admin_dash.php" : "superadmin_dash.php"));
  exit();
}
