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

// Ensure CSRF token is set
if (!isset($_SESSION['token'])) {
  $_SESSION['token'] = bin2hex(random_bytes(32)); // Secure 32-byte token
}

// Handle User Registration
if (is_post_request() && isset($_POST['register'])) {
  // Validate CSRF Token
  if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
    $_SESSION['message'] = "error_csrf_invalid";
    header("Location: ../signup.php");
    exit();
  }

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
    $_SESSION['message'] = "error_fields_blank";
    header("Location: ../signup.php");
    exit();
  }
  if (!has_length($password, ['min' => 8])) {
    $_SESSION['message'] = "error_password_short";
    header("Location: ../signup.php");
    exit();
  }
  if ($password !== $confirm_password) {
    $_SESSION['message'] = "error_password_mismatch";
    header("Location: ../signup.php");
    exit();
  }
  if (!has_valid_email_format($email)) {
    $_SESSION['message'] = "error_invalid_email";
    header("Location: ../signup.php");
    exit();
  }
  if (!has_unique_username($username)) {
    $_SESSION['message'] = "error_username_taken";
    header("Location: ../signup.php");
    exit();
  }

  if (empty($errors)) {
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert into Users Table
    $sql = "INSERT INTO users (username, first_name, last_name, email, password, user_level_id, is_active) 
                VALUES (?, ?, ?, ?, ?, ?, 1)";
    $stmt = $db->prepare($sql);
    $stmt->execute([$username, $fname, $lname, $email, $hashed_password, $is_vendor ? 2 : 1]);
    $user_id = $db->lastInsertId();

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

      $_SESSION['message'] = "vendor_pending";
      $redirect_url = "/vendor_dash.php";
    } else {
      $_SESSION['message'] = "user_registered";
      $redirect_url = "/dashboard.php";
    }

    // Set Session Variables
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['user_level_id'] = $is_vendor ? 2 : 1;
    $_SESSION['profile_image'] = $profile_image;

    header("Location: " . $redirect_url);
    exit();
  }
}

// 🚀 Handle User Login
if (is_post_request() && isset($_POST['login'])) {
  // Validate CSRF Token
  if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
    $_SESSION['message'] = "error_csrf_invalid";
    header("Location: ../login.php");
    exit();
  }

  $username = h($_POST['username']);
  $password = $_POST['password'];

  if (is_blank($username) || is_blank($password)) {
    $_SESSION['message'] = "error_invalid_login";
    header("Location: ../login.php");
    exit();
  }

  $sql = "SELECT u.user_id, u.username, u.password, u.user_level_id, u.is_active, v.vendor_status
            FROM users u
            LEFT JOIN vendor v ON u.user_id = v.user_id
            WHERE u.username = ?";
  $stmt = $db->prepare($sql);
  $stmt->execute([$username]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user && password_verify($password, $user['password'])) {
    if ($user['is_active'] == 0) {
      $_SESSION['message'] = "error_account_inactive";
      header("Location: ../login.php");
      exit();
    }

    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['user_level_id'] = $user['user_level_id'];
    $_SESSION['profile_image'] = get_profile_image($user['user_id']);

    $_SESSION['message'] = "login_success";

    if ($_SESSION['user_level_id'] == 2) {
      header("Location: ../vendor_dash.php");
    } elseif ($_SESSION['user_level_id'] == 3) {
      header("Location: ../admin_dash.php");
    } elseif ($_SESSION['user_level_id'] == 4) {
      header("Location: ../superadmin_dash.php");
    } else {
      header("Location: ../dashboard.php");
    }
    exit();
  } else {
    $_SESSION['message'] = "error_invalid_login";
    header("Location: ../login.php");
    exit();
  }
}

// Handle Logout
if (is_get_request() && isset($_GET['logout'])) {
  session_destroy();
  $_SESSION['message'] = "logout_success";
  header("Location: ../login.php");
  exit();
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
