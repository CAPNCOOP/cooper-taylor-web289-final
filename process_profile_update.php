<?php
require_once 'private/initialize.php';
require_once 'private/functions.php';
require_once 'private/validation_functions.php';
require_login();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: edit_profile.php?message=error_invalid_request");
  exit();
}

$user_id = $_SESSION['user_id'] ?? null;
$user_level = $_SESSION['user_level_id'] ?? null;

if (!$user_id) {
  header("Location: login.php?error=session_expired");
  exit();
}

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');

// ✅ Uniqueness Check for Username
if (User::isUsernameTaken($username, $user_id)) {
  $_SESSION['form_data'] = $_POST;
  $session->message("❌ That username is already taken.");
  redirect_to('edit_profile.php');
  exit();
}

// ✅ Uniqueness Check for Email (optional)
if (User::isEmailTaken($email, $user_id)) {
  $_SESSION['form_data'] = $_POST;
  $session->message("❌ That email is already in use.");
  redirect_to('edit_profile.php');
  exit();
}

// ✅ Update user profile
$user = User::find_by_id($user_id);
$user->username = $username;
$user->email = $email;
$update_success = $user->save();

// ✅ Handle Profile Image Upload
if (!empty($_FILES['profile_image']['name']) && $_FILES['profile_image']['error'] == 0) {
  $full_name = strtolower($user->first_name . '_' . $user->last_name);
  $file_extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
  $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];

  if (!in_array(strtolower($file_extension), $allowed_extensions)) {
    header("Location: edit_profile.php?message=error_invalid_image");
    exit();
  }

  $profile_image = 'img/upload/users/' . $full_name . '.' . $file_extension;

  $src_image = match (strtolower($file_extension)) {
    'jpg', 'jpeg' => imagecreatefromjpeg($_FILES['profile_image']['tmp_name']),
    'png' => imagecreatefrompng($_FILES['profile_image']['tmp_name']),
    'webp' => imagecreatefromwebp($_FILES['profile_image']['tmp_name']),
    default => false,
  };

  if ($src_image) {
    $orig_width = imagesx($src_image);
    $orig_height = imagesy($src_image);
    $crop_size = min($orig_width, $orig_height);
    $x_offset = ($orig_width - $crop_size) / 2;
    $y_offset = ($orig_height - $crop_size) / 2;
    $cropped_image = imagecreatetruecolor(500, 500);
    imagecopyresampled($cropped_image, $src_image, 0, 0, $x_offset, $y_offset, 500, 500, $crop_size, $crop_size);

    match (strtolower($file_extension)) {
      'jpg', 'jpeg' => imagejpeg($cropped_image, $profile_image, 90),
      'png' => imagepng($cropped_image, $profile_image, 9),
      'webp' => imagewebp($cropped_image, $profile_image, 90),
    };

    imagedestroy($src_image);
    imagedestroy($cropped_image);

    $sql = "INSERT INTO profile_image (user_id, file_path) VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE file_path = VALUES(file_path)";
    $stmt = $db->prepare($sql);
    $stmt->execute([$user_id, $profile_image]);

    $_SESSION['profile_image'] = $profile_image;
  }
}

// ✅ If Vendor, Update Vendor-Specific Details
if ($user_level == 2) {
  $vendor = Vendor::find_by_user_id($user_id);

  if (!$vendor || !$vendor->vendor_id) {
    $_SESSION['form_data'] = $_POST;
    $session->message("❌ Vendor record not found or incomplete. Cannot update profile.");
    redirect_to('edit_profile.php');
    exit();
  }

  // ✅ Patch in the user_id if it's missing
  if (empty($vendor->user_id)) {
    $vendor->user_id = $user_id;
  }

  $vendor->business_name = trim($_POST['business_name']);
  $vendor->business_EIN = trim($_POST['business_ein']);
  $vendor->contact_number = trim($_POST['contact_number']);
  $vendor->description = trim($_POST['description']);

  if (!$vendor->save()) {
    $_SESSION['form_data'] = $_POST;
    header("Location: edit_profile.php?message=error_update_failed");
    exit();
  }
}

$_SESSION['username'] = $username;

if ($update_success) {
  header("Location: edit_profile.php?message=profile_updated");
} else {
  $_SESSION['form_data'] = $_POST;
  header("Location: edit_profile.php?message=error_update_failed");
}
exit();
