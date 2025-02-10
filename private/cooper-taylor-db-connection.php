<?php
// local
$host = 'localhost';
$dbname = 'farmers_market';
$username = 'root';  // Default Laragon MySQL user
$password = '';

// SiteGround
// define("DB_SERVER", "localhost");
// define("DB_USER", "uamij6bxstfg3");
// define("DB_PASS", "w1ckedp155er!!");
// define("DB_NAME", "dbzlvmnyeq51y6");

try {
  $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Database connection failed: " . $e->getMessage());
}
