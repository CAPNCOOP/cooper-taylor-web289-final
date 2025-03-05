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

// Fetch user details
$sql = "SELECT first_name, last_name FROM users WHERE user_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id]);
$user_info = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if first_name and last_name are retrieved correctly
$first_name = $user_info['first_name'];  // Use the first name from the database
$last_name = $user_info['last_name'];    // Use the last name from the database

// Initialize the profile image variable to the current session value
$old_profile_image = $_SESSION['profile_image']; // The old image path stored in the session

// Handle Profile Image Upload (if provided)
if (!empty($_FILES['profile_image']['name'])) {
  // If there's an old profile image, delete it from the folder
  if ($old_profile_image && file_exists($old_profile_image)) {
    // Get the base name (without file extension) for both old and new images
    $base_name = strtolower($first_name . '_' . $last_name); // Example: "john_doe"

    // Loop through accepted extensions and delete the old image if it exists
    $accepted_extensions = ['.jpg', '.jpeg', '.png', '.webp'];
    foreach ($accepted_extensions as $ext) {
      $old_image_path = 'img/upload/users/' . $base_name . $ext; // Generate the path with the correct extension
      if (file_exists($old_image_path)) {
        unlink($old_image_path);  // Delete the old image
      }
    }
  }

  // Sanitize the first and last name for the new image
  $full_name = strtolower($first_name . '_' . $last_name);  // Example: "john_doe"

  // Get the file extension of the uploaded image
  $file_extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
  $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];

  // Ensure the file extension is allowed
  if (!in_array(strtolower($file_extension), $allowed_extensions)) {
    $_SESSION['message'] = 'Invalid file type. Please upload a JPG, PNG, or WebP file.';
    header("Location: edit_profile.php");
    exit;
  }

  // Set the new profile image filename (with the correct extension)
  $profile_image = 'img/upload/users/' . $full_name . '.' . $file_extension;

  // Check if the uploaded file is a valid image
  if (!getimagesize($_FILES['profile_image']['tmp_name'])) {
    $_SESSION['message'] = 'Uploaded file is not a valid image. Please upload a valid image.';
    header("Location: edit_profile.php");
    exit;
  }

  // Handle image resizing and cropping
  $src_image = null;

  // Create the source image resource based on the file type
  if ($file_extension == 'jpg' || $file_extension == 'jpeg') {
    $src_image = imagecreatefromjpeg($_FILES['profile_image']['tmp_name']);
  } elseif ($file_extension == 'png') {
    $src_image = imagecreatefrompng($_FILES['profile_image']['tmp_name']);
  } elseif ($file_extension == 'webp') {
    $src_image = imagecreatefromwebp($_FILES['profile_image']['tmp_name']);
  }

  // Check if the image resource is created successfully
  if ($src_image === false) {
    $_SESSION['message'] = 'Failed to create the image resource. Please upload a valid image file.';
    header("Location: edit_profile.php");
    exit;
  }

  // Get the original image dimensions
  $orig_width = imagesx($src_image);
  $orig_height = imagesy($src_image);

  // Calculate the center crop
  $crop_size = min($orig_width, $orig_height);
  $x_offset = ($orig_width - $crop_size) / 2;
  $y_offset = ($orig_height - $crop_size) / 2;

  // Create a new image of the crop size
  $cropped_image = imagecreatetruecolor(500, 500);

  // Crop and resize the image to 500x500
  imagecopyresampled($cropped_image, $src_image, 0, 0, $x_offset, $y_offset, 500, 500, $crop_size, $crop_size);

  // Save the cropped image to the desired location
  if ($file_extension == 'jpg' || $file_extension == 'jpeg') {
    imagejpeg($cropped_image, $profile_image, 90); // Save as JPEG with 90 quality
  } elseif ($file_extension == 'png') {
    imagepng($cropped_image, $profile_image, 9); // Save as PNG with max compression
  } elseif ($file_extension == 'webp') {
    imagewebp($cropped_image, $profile_image, 90); // Save as WebP with 90 quality
  }

  // Free up memory
  imagedestroy($src_image);
  imagedestroy($cropped_image);

  // Update profile_image table
  $sql = "INSERT INTO profile_image (user_id, file_path) VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE file_path = VALUES(file_path)";
  $stmt = $db->prepare($sql);
  $stmt->execute([$user_id, $profile_image]);

  // Update session variable with new profile image
  $_SESSION['profile_image'] = $profile_image;
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

// Assuming user level is stored in the session after login
if (isset($_SESSION['user_level_id'])) {
  switch ($_SESSION['user_level_id']) {
    case 1: // Regular user
      header("Location: dashboard.php?success=profile_updated");
      break;
    case 2: // Vendor
      header("Location: vendor_dash.php?success=profile_updated");
      break;
    case 3: // Admin
      header("Location: admin_dash.php?success=profile_updated");
      break;
    case 4: // Super Admin
      header("Location: superadmin_dash.php?success=profile_updated");
      break;
    default: // Fallback if user level is unknown
      header("Location: index.php?error=invalid_user_level_id");
  }
} else {
  header("Location: login.php?error=session_expired");
}

exit;
