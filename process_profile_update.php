<?php
require_once 'private/initialize.php';
require_once 'private/functions.php';
require_once 'private/validation_functions.php';
require_login();

$user_id = $_SESSION['user_id'];
$user_level = $_SESSION['user_level_id'];
$username = h($_POST['username']);
$email = h($_POST['email']);
$profile_image = $_SESSION['profile_image']; // Default to current image

// Handle Profile Image Upload (if provided)
if (!empty($_FILES['profile_image']['name'])) {
  $profile_image = upload_image($_FILES['profile_image'], 'users'); // Upload & get path
}

// Update `users` table (DO NOT include profile_image, it does not exist there)
$sql = "UPDATE users SET username = ?, email = ? WHERE user_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$username, $email, $user_id]);

// Update or Insert into `profile_image` table (fixing ON DUPLICATE KEY)
if (!empty($profile_image)) {
  $sql = "INSERT INTO profile_image (user_id, file_path) VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE file_path = VALUES(file_path)";
  $stmt = $db->prepare($sql);
  $stmt->execute([$user_id, $profile_image]);
}

// If Vendor, Update Vendor-Specific Details
if ($user_level == 2) {
  $business_name = h($_POST['business_name']);
  $business_ein = h($_POST['business_ein']);
  $contact_number = h($_POST['contact_number']);
  $description = h($_POST['description']);

  $sql = "UPDATE vendor SET business_name = ?, business_EIN = ?, contact_number = ?, description = ? WHERE user_id = ?";
  $stmt = $db->prepare($sql);
  $stmt->execute([$business_name, $business_ein, $contact_number, $description, $user_id]);
}

// Refresh session variables
$_SESSION['username'] = $username;
$_SESSION['profile_image'] = $profile_image; // Correctly store updated profile image

// Redirect Back to Dashboard
header("Location: dashboard.php?success=profile_updated");
exit;
