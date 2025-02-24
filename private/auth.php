<?php
require_once 'initialize.php';
require_once 'validation_functions.php';
require_once 'functions.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$errors = [];

// Image Upload Function
function upload_image($file, $folder)
{
  $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
  $upload_dir = __DIR__ . "/../img/upload/{$folder}/";
  if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true); // Create folder if it doesn't exist
  }

  if ($file['error'] === UPLOAD_ERR_OK) {
    $file_type = mime_content_type($file['tmp_name']);
    if (!in_array($file_type, $allowed_types)) {
      return null; // Invalid file type
    }

    // Generate unique filename
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = uniqid("img_", true) . '.' . $ext;
    $target_path = $upload_dir . $new_filename;

    // Resize Image
    $image = imagecreatefromstring(file_get_contents($file['tmp_name']));
    $resized_image = imagescale($image, 500, 500);
    switch ($file_type) {
      case 'image/jpeg':
        imagejpeg($resized_image, $target_path);
        break;
      case 'image/png':
        imagepng($resized_image, $target_path);
        break;
      case 'image/webp':
        imagewebp($resized_image, $target_path);
        break;
    }
    imagedestroy($image);
    imagedestroy($resized_image);

    return "img/upload/{$folder}/" . $new_filename;
  }
  return null;
}

// Handle user registration
if (is_post_request() && isset($_POST['register'])) {
  $username = h($_POST['username']);
  $fname = h($_POST['fname']);
  $lname = h($_POST['lname']);
  $email = h($_POST['email']);
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm-pass'];
  $is_vendor = $_POST['vendorRequest'] === 'yes' ? 1 : 0;
  $ein = $is_vendor ? h($_POST['business_EIN']) : NULL;

  // Image Upload Handling
  $profile_image = isset($_FILES['profile_image']) ? upload_image($_FILES['profile_image'], 'users') : 'img/upload/users/default.png';

  // Validation
  if (is_blank($username) || is_blank($email) || is_blank($password)) {
    $errors[] = "Required fields cannot be blank.";
  }
  if (!has_length($password, ['min' => 8])) {
    $errors[] = "Password must be at least 8 characters.";
  }
  if ($password !== $confirm_password) {
    $errors[] = "Passwords do not match.";
  }
  if (!has_valid_email_format($email)) {
    $errors[] = "Invalid email format.";
  }
  if (!has_unique_username($username)) {
    $errors[] = "Username is already taken.";
  }

  if (empty($errors)) {
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    if ($is_vendor) {
      // Vendor-Specific Fields
      $business_name = h($_POST['business_name']);
      $business_address = h($_POST['business_address']);

      // Business Image Upload
      $business_image = isset($_FILES['business_image']) ? upload_image($_FILES['business_image'], 'vendors') : 'img/upload/vendors/default.png';

      // Insert into Users Table
      $sql = "INSERT INTO users (username, first_name, last_name, email, password, profile_image, user_level_id) VALUES (?, ?, ?, ?, ?, ?, 2)";
      $stmt = $db->prepare($sql);
      $stmt->execute([$username, $fname, $lname, $email, $hashed_password, $profile_image]);
      $user_id = $db->lastInsertId();

      // Insert into Vendors Table
      $sql = "INSERT INTO vendor (user_id, business_name, business_EIN, business_address, business_image, vendor_status) VALUES (?, ?, ?, ?, ?, 'pending')";
      $stmt = $db->prepare($sql);
      $stmt->execute([$user_id, $business_name, $ein, $business_address, $business_image]);
    } else {
      // Insert into Users Table (Regular Member)
      $sql = "INSERT INTO users (username, first_name, last_name, email, password, profile_image, user_level_id) VALUES (?, ?, ?, ?, ?, ?, 1)";
      $stmt = $db->prepare($sql);
      $stmt->execute([$username, $fname, $lname, $email, $hashed_password, $profile_image]);
    }

    redirect_to('login.php?signup_success=1');
  }
}

// Handle user login
if (is_post_request() && isset($_POST['login'])) {
  $username = h($_POST['username']);
  $password = $_POST['password'];

  if (is_blank($username) || is_blank($password)) {
    $errors[] = "Username and password cannot be blank.";
  }

  if (empty($errors)) {
    $sql = "SELECT user_id, username, password, user_level_id, user_status, profile_image FROM users WHERE username = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
      if ($user['user_status'] !== 'approved') {
        redirect_to('login.php?error=account_pending');
      }

      $_SESSION['user_id'] = $user['user_id'];
      $_SESSION['username'] = $user['username'];
      $_SESSION['user_level_id'] = $user['user_level_id'];
      $_SESSION['profile_image'] = $user['profile_image'];

      redirect_to('dashboard.php');
    } else {
      redirect_to('login.php?error=invalid_credentials');
    }
  }
}

// Handle logout
if (is_get_request() && isset($_GET['logout'])) {
  session_destroy();
  redirect_to('login.php');
}
