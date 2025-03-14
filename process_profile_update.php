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

// Fetch user details
$sql = "SELECT first_name, last_name FROM users WHERE user_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id]);
$user_info = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user_info) {
  header("Location: edit_profile.php?message=error_update_failed");
  exit();
}

$first_name = $user_info['first_name'];
$last_name = $user_info['last_name'];

// Fetch current session values
$username = h($_POST['username'] ?? '');
$email = h($_POST['email'] ?? '');
$profile_image = $_SESSION['profile_image'] ?? 'img/upload/users/default.png'; // Default profile image

// ✅ Update user profile
$sql = "UPDATE users SET username = ?, email = ? WHERE user_id = ?";
$stmt = $db->prepare($sql);
$update_success = $stmt->execute([$username, $email, $user_id]);

// ✅ Handle Profile Image Upload
if (!empty($_FILES['profile_image']['name']) && $_FILES['profile_image']['error'] == 0) {
  $full_name = strtolower($first_name . '_' . $last_name);
  $file_extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
  $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];

  if (!in_array(strtolower($file_extension), $allowed_extensions)) {
    header("Location: edit_profile.php?message=error_invalid_image");
    exit();
  }

  $profile_image = 'img/upload/users/' . $full_name . '.' . $file_extension;

  // Resize & Save Image
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

    // Save the cropped image
    match (strtolower($file_extension)) {
      'jpg', 'jpeg' => imagejpeg($cropped_image, $profile_image, 90),
      'png' => imagepng($cropped_image, $profile_image, 9),
      'webp' => imagewebp($cropped_image, $profile_image, 90),
    };

    imagedestroy($src_image);
    imagedestroy($cropped_image);

    // Update profile image in DB
    $sql = "INSERT INTO profile_image (user_id, file_path) VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE file_path = VALUES(file_path)";
    $stmt = $db->prepare($sql);
    $stmt->execute([$user_id, $profile_image]);

    $_SESSION['profile_image'] = $profile_image;
  }
}

// ✅ If Vendor, Update Vendor-Specific Details
if ($user_level == 2) {
  $business_name = h($_POST['business_name']);
  $business_ein = h($_POST['business_ein']);
  $contact_number = h($_POST['contact_number']);
  $description = h($_POST['description']);

  $sql = "UPDATE vendor SET business_name = ?, business_EIN = ?, contact_number = ?, description = ? WHERE user_id = ?";
  $stmt = $db->prepare($sql);
  $update_success_vendor = $stmt->execute([$business_name, $business_ein, $contact_number, $description, $user_id]);

  if (!$update_success_vendor) {
    header("Location: edit_profile.php?message=error_update_failed");
    exit();
  }
}

// ✅ Refresh session variables
$_SESSION['username'] = $username;

// ✅ Redirect with success message
if ($update_success) {
  header("Location: edit_profile.php?message=profile_updated");
} else {
  header("Location: edit_profile.php?message=error_update_failed");
}
exit();
