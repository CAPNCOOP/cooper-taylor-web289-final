<?php
require_once 'private/initialize.php';

$password = "SuperAdminPass123"; // Make sure this is the correct password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

$sql = "UPDATE users SET password = ? WHERE username = 'superadmin_user'";
$stmt = $db->prepare($sql);
$stmt->execute([$hashedPassword]);

echo "✅ Super Admin password updated with a correct PHP-generated hash!";
