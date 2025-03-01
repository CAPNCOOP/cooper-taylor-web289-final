<?php
ob_start();
require_once 'initialize.php';
require_once 'validation_functions.php';
require_once 'functions.php';

ob_end_flush();

$errors = [];
$username = $password = $confirm_password = $email = "";
$fname = $lname = $is_vendor = $ein = null;
$profile_image = 'img/upload/users/default.png';

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

  // Validation
  if (is_blank($username) || is_blank($email) || is_blank($password)) {
    die("❌ Required fields cannot be blank.");
  }
  if (!has_length($password, ['min' => 8])) {
    die("❌ Password must be at least 8 characters.");
  }
  if ($password !== $confirm_password) {
    die("❌ Passwords do not match.");
  }
  if (!has_valid_email_format($email)) {
    die("❌ Invalid email format.");
  }
  if (!has_unique_username($username)) {
    die("❌ Username already taken.");
  }

  if (empty($errors)) {
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert into Users Table
    $sql = "INSERT INTO users (username, first_name, last_name, email, password, user_level_id, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, 1)";
    $stmt = $db->prepare($sql);
    $stmt->execute([$username, $fname, $lname, $email, $hashed_password, $is_vendor ? 2 : 1]);
    $user_id = $db->lastInsertId();

    // Image Upload Handling
    if (!empty($_FILES['profile_image']['name'])) {
      $full_name = strtolower($_POST['fname'] . '_' . $_POST['lname']);
      $profile_image = upload_image($_FILES['profile_image'], 'users', $full_name);
    }

    // Save profile image path
    $sql = "INSERT INTO profile_image (user_id, file_path) VALUES (?, ?) 
        ON DUPLICATE KEY UPDATE file_path = VALUES(file_path)";
    $stmt = $db->prepare($sql);
    $stmt->execute([$user_id, $profile_image]);

    // Insert into Vendor Table if Vendor
    if ($is_vendor) {
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

      $sql = "INSERT INTO vendor (user_id, business_name, contact_number, business_EIN, business_email, website, city, state_id, street_address, zip_code, description, vendor_bio, vendor_status) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
      $stmt = $db->prepare($sql);
      $stmt->execute([$user_id, $business_name, $contact_number, $ein, $business_email, $website, $city, $state_id, $street_address, $zip_code, $description, $vendor_bio]);
    }

    ob_end_clean(); // Clear output before redirecting

    // Set Session Variables
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['user_level_id'] = $is_vendor ? 2 : 1;
    $_SESSION['profile_image'] = $profile_image;

    // Redirect Based on User Level
    $redirect_url = ($is_vendor) ? "/vendor_dash.php" : "/dashboard.php";
    header("Location: " . $redirect_url);
    exit;
  }
}

// Handle User Login
if (is_post_request() && isset($_POST['login'])) {
  $username = h($_POST['username']);
  $password = $_POST['password'];

  if (is_blank($username) || is_blank($password)) {
    header("Location: login.php?error=invalid_credentials");
    exit;
  }

  $sql = "SELECT user_id, username, password, user_level_id, is_active FROM users WHERE username = ?";
  $sql = "SELECT u.user_id, u.username, u.password, u.user_level_id, u.is_active, v.vendor_status
  FROM users u
  LEFT JOIN vendor v ON u.user_id = v.user_id
  WHERE u.username = ?";

  $stmt = $db->prepare($sql);
  $stmt->execute([$username]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user && password_verify($password, $user['password'])) {
    if ($user['is_active'] == 0) {
      header("Location: login.php?error=account_inactive");
      exit;
    }

    session_regenerate_id(true);

    // Set Correct Session Variables
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['user_level_id'] = $user['user_level_id'];
    $_SESSION['profile_image'] = get_profile_image($user['user_id']);

    // Redirect Based on User Level
    if ($_SESSION['user_level_id'] == 2) {
      header("Location: /vendor_dash.php");
    } elseif ($_SESSION['user_level_id'] == 3) { // Admin
      header("Location: /admin_dash.php");
    } elseif ($_SESSION['user_level_id'] == 4) { // Super Admin
      header("Location: /superadmin_dash.php");
    } else {
      header("Location: /dashboard.php");
    }
    exit;
  } else {
    header("Location: login.php?error=invalid_credentials");
    exit;
  }
}

if ($_SESSION['user_level_id'] == 2 && isset($user['vendor_status'])) {
  if ($user['vendor_status'] == 'pending') {
    die("❌ Error: Your vendor account is pending approval.");
  } elseif ($user['vendor_status'] == 'denied') {
    die("❌ Error: Your vendor application was denied. Contact support for more info.");
  }
}

// Handle Logout
if (is_get_request() && isset($_GET['logout'])) {
  session_destroy();
  header("Location: login.php");
  exit;
}

// Function to Get Profile Image
function get_profile_image($user_id)
{
  global $db;
  $sql = "SELECT file_path FROM profile_image WHERE user_id = ?";
  $stmt = $db->prepare($sql);
  $stmt->execute([$user_id]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  return $result ? $result['file_path'] : 'img/upload/users/default.png';
}
