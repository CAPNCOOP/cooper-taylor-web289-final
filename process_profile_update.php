<?php
require_once 'private/initialize.php';
require_once 'private/functions.php';
require_once 'private/validation_functions.php';
require_login();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  $session->message("❌ Error: Invalid request method.");
  header("Location: edit_profile.php");
  exit;
}

$user_id = $_SESSION['user_id'] ?? null;
$user_level = $_SESSION['user_level_id'] ?? null;

if (!$user_id) {
  $session->message("⚠️ Session expired. Please log in again.");
  header("Location: login.php");
  exit;
}

$username = strip_tags(trim($_POST['username'] ?? ''));
$email = strip_tags(trim($_POST['email'] ?? ''));

// Uniqueness Check for Username
if (User::isUsernameTaken($username, $user_id)) {
  $_SESSION['form_data'] = $_POST;
  $session->message("❌ That username is already taken.");
  redirect_to('edit_profile.php');
  exit();
}

// Uniqueness Check for Email (optional)
if (User::isEmailTaken($email, $user_id)) {
  $_SESSION['form_data'] = $_POST;
  $session->message("❌ That email is already in use.");
  redirect_to('edit_profile.php');
  exit();
}

// Update user profile
$user = User::find_by_id($user_id);
$user->username = $username;
$user->email = $email;

// Handle password update
$new_password = trim($_POST['new_password'] ?? '');
$confirm_password = trim($_POST['confirm_password'] ?? '');

if (!empty($new_password) || !empty($confirm_password)) {
  if ($new_password !== $confirm_password) {
    $_SESSION['form_data'] = $_POST;
    $session->message("❌ Error: Passwords do not match.");
    redirect_to('edit_profile.php');
    exit;
  }

  if (strlen($new_password) < 8) {
    $_SESSION['form_data'] = $_POST;
    $session->message("❌ Error: Password must be at least 8 characters.");
    redirect_to('edit_profile.php');
    exit;
  }

  $user->password = password_hash($new_password, PASSWORD_DEFAULT);
}

// Now save everything
$update_success = $user->save();

// Handle Profile Image Upload
if (!empty($_FILES['profile_image']['name']) && $_FILES['profile_image']['error'] == 0) {
  $full_name = strtolower($user->first_name . '_' . $user->last_name);
  $uploaded_file = handle_cropped_upload('cropped-profile', 'users', $full_name, $user_id);

  if (!$uploaded_file && !empty($_FILES['profile_image']['name'])) {
    $uploaded_file = upload_image($_FILES['profile_image'], 'users', $full_name, $user_id);
  }

  if ($uploaded_file) {
    $profile_image_path = 'users/' . $uploaded_file;

    $sql = "INSERT INTO profile_image (user_id, file_path)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE file_path = VALUES(file_path)";
    $stmt = $db->prepare($sql);
    $stmt->execute([$user_id, $profile_image_path]);

    $_SESSION['profile_image'] = $profile_image_path;
  }
}

// If Vendor, Update Vendor-Specific Details
if ($user_level == 2) {
  $vendor = Vendor::find_by_user_id($user_id);

  if (!$vendor || !$vendor->vendor_id) {
    $_SESSION['form_data'] = $_POST;
    $session->message("❌ Vendor record not found or incomplete. Cannot update profile.");
    redirect_to('edit_profile.php');
    exit();
  }

  //Patch in the user_id if it's missing
  if (empty($vendor->user_id)) {
    $vendor->user_id = $user_id;
  }

  $vendor->business_name = strip_tags(trim($_POST['business_name']));
  $vendor->business_EIN = strip_tags(trim($_POST['business_ein']));
  $vendor->contact_number = strip_tags(trim($_POST['contact_number']));
  $vendor->description = strip_tags(trim($_POST['description']));
  $vendor->vendor_bio = strip_tags(trim($_POST['vendor_bio']));

  if (!$vendor->save()) {
    $session->message("❌ Error: Profile update failed.");
    header("Location: edit_profile.php");
    exit;
  }
}

$_SESSION['username'] = $username;

if ($update_success) {
  $session->message("✅ Profile updated successfully!");
  header("Location: edit_profile.php");
  exit;
} else {
  $session->message("❌ Error: Profile update failed.");
  header("Location: edit_profile.php");
  exit;
}
