<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();

require_once __DIR__ . '/initialize.php';
require_once __DIR__ . '/validation_functions.php';
require_once __DIR__ . '/functions.php';


ob_end_flush();

$errors = [];
$username = $password = $confirm_password = $email = "";
$fname = $lname = $is_vendor = $ein = null;
$profile_image = 'img/upload/users/default.png';

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
  $username = h($_POST['username'] ?? '');
  $fname = h($_POST['fname'] ?? '');
  $lname = h($_POST['lname'] ?? '');
  $email = h($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $confirm_password = $_POST['confirm-pass'] ?? '';
  $is_vendor = isset($_POST['is_vendor']) ? (int)$_POST['is_vendor'] : 0;
  $ein = $is_vendor ? h($_POST['business_EIN'] ?? '') : NULL;


  // Image Upload Handling
  $profile_image = isset($_FILES['profile_image']) ? upload_image($_FILES['profile_image'], 'users') : 'img/upload/users/default.png';

  // Validation
  if (is_blank($username) || is_blank($email) || is_blank($password)) {
    $errors[] = "Required fields cannot be blank.";
    die("Validation failed: Required fields cannot be blank.");
  }
  if (!has_length($password, ['min' => 8])) {
    $errors[] = "Password must be at least 8 characters.";
    die("Validation failed: Password must be at least 8 characters.");
  }
  if ($password !== $confirm_password) {
    $errors[] = "Passwords do not match.";
    die("Validation failed: Passwords do not match.");
  }
  if (!has_valid_email_format($email)) {
    $errors[] = "Invalid email format.";
    die("Validation failed: Invalid email format.");
  }
  if (!has_unique_username($username)) {
    $errors[] = "Username is already taken.";
    die("Validation failed: Username already taken.");
  }

  if (empty($errors)) {
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert into Users Table
    $sql = "INSERT INTO users (username, first_name, last_name, email, password, user_level_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->execute([$username, $fname, $lname, $email, $hashed_password, $is_vendor ? 2 : 1]);
    $user_id = $db->lastInsertId();

    // Store Profile Image in `profile_image` Table
    if (!empty($_FILES['profile_image']['name'])) {
      $sql = "INSERT INTO profile_image (user_id, file_path) VALUES (?, ?)";
      $stmt = $db->prepare($sql);
      $stmt->execute([$user_id, $profile_image]);
    }
  }

  // Insert into Vendor Table if Vendor
  if ($is_vendor) {
    // Vendor-Specific Fields
    $business_name = h($_POST['business_name']);
    $contact_number = h($_POST['contact_number']);
    $business_email = h($_POST['business_email']);
    $website = h($_POST['website']);
    $city = h($_POST['city']);
    $state_id = h($_POST['state_id']);
    $street_address = h($_POST['street_address']);
    $zip_code = h($_POST['zip_code']);
    $description = h($_POST['description']);
    $vendor_bio = h($_POST['vendor_bio']);

    // Insert into Users Table (Vendor Account)
    // User ID is already created in previous step

    // Insert into Vendor Table
    $sql = "INSERT INTO vendor (user_id, business_name, contact_number, business_EIN, business_email, website, city, state_id, street_address, zip_code, description, vendor_bio, vendor_status) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
    $stmt = $db->prepare($sql);
    $stmt->execute([$user_id, $business_name, $contact_number, $ein, $business_email, $website, $city, $state_id, $street_address, $zip_code, $description, $vendor_bio]);
  }
  ob_end_clean(); // Clear any unexpected output before redirecting

  $_SESSION['user_id'] = $user_id;
  $_SESSION['username'] = $username;
  $_SESSION['user_level_id'] = $is_vendor ? 2 : 1;
  $_SESSION['profile_image'] = $profile_image;

  echo "<pre>✅ Session Before Redirecting: ";
  print_r($_SESSION);
  echo "</pre>";
  exit;

  $redirect_url = ($is_vendor) ? "/vendor_dash.php" : "/dashboard.php";
  header("Location: " . $redirect_url);
  exit;
}

// Handle user login
if (is_post_request() && isset($_POST['login'])) {

  $username = h($_POST['username']);
  $password = $_POST['password'];


  if (is_blank($username) || is_blank($password)) {
    $errors[] = "Username and password cannot be blank.";
  }

  if (empty($errors)) {
    $sql = "SELECT user_id, username, password, user_level_id FROM users WHERE username = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
      session_regenerate_id(true); // Prevent session fixation attacks

      $_SESSION['user_id'] = $user['user_id'];
      $_SESSION['username'] = $user['username'];
      $_SESSION['user_level'] = $user['user_level'];

      $session->login($user); // Ensure Session class tracks the login

      redirect_to('/dashboard.php');
    } else {
      redirect_to('/login.php?error=invalid_credentials');
    }
  }
}


// Handle logout
if (is_get_request() && isset($_GET['logout'])) {
  session_destroy();
  redirect_to('/login.php');
}
