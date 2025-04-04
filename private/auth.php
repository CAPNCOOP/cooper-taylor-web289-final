<?php
require_once 'initialize.php';
require_once 'validation_functions.php';
require_once 'functions.php';

$errors = [];
$username = $password = $confirm_password = $email = "";
$fname = $lname = $is_vendor = $ein = null;
$profile_image = 'img/upload/users/default.png';

// Handle User Registration
if (is_post_request() && $_POST['register'] == '1') {
  $username = $_POST['username'] ?? '';
  $fname = $_POST['fname'] ?? '';
  $lname = $_POST['lname'] ?? '';
  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';
  $confirm_password = $_POST['confirm-pass'] ?? '';
  $is_vendor = isset($_POST['is_vendor']) ? (int)$_POST['is_vendor'] : 0;
  $ein = $is_vendor ? $_POST['business_EIN'] ?? '' : NULL;
  $clean_name = strtolower($fname . '_' . $lname . '_' . $user_id);
  $uploaded_filename = upload_image($_FILES['profile-pic'], 'users', $clean_name);



  // Validation
  if (is_blank($username) || is_blank($email) || is_blank($password)) {
    $_SESSION['message'] = "❌ Required fields cannot be blank.";
    header("Location: ../signup.php");
    exit();
  }
  if (!has_length($password, ['min' => 8])) {
    $_SESSION['message'] = "❌ Password must be at least 8 characters.";
    header("Location: ../signup.php");
    exit();
  }
  if ($password !== $confirm_password) {
    $_SESSION['message'] = "❌ Passwords do not match.";
    header("Location: ../signup.php");
    exit();
  }
  if (!has_valid_email_format($email)) {
    $_SESSION['message'] = "❌ Invalid email format.";
    header("Location: ../signup.php");
    exit();
  }
  if (!has_unique_username($username)) {
    $_SESSION['message'] = "❌ Username already taken.";
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

    // Handle Profile Image Upload
    $uploaded_filename = null;
    if (isset($_FILES['profile-pic']) && $_FILES['profile-pic']['error'] === UPLOAD_ERR_OK) {
      $uploaded_filename = upload_image($_FILES['profile-pic'], 'users', $clean_name);
    }

    $profile_image = $uploaded_filename
      ? "img/upload/users/" . $uploaded_filename
      : 'img/upload/users/default.png';

    $sql = "INSERT INTO profile_image (user_id, file_path) VALUES (?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->execute([$user_id, $profile_image]);

    // Insert into Vendor Table if Vendor
    if ($is_vendor) {
      $business_name = $_POST['business_name'];
      $contact_number = $_POST['contact_number'];
      $business_email = $_POST['business_email'];
      $website = $_POST['website'];
      $city = $_POST['city'];
      $state_id = $_POST['state_id'];
      $street_address = $_POST['street_address'];
      $zip_code = $_POST['zip_code'];
      $description = $_POST['description'];
      $vendor_bio = $_POST['vendor_bio'];

      $sql = "INSERT INTO vendor (user_id, business_name, contact_number, business_EIN, business_email, website, city, state_id, street_address, zip_code, description, vendor_bio, vendor_status) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
      $stmt = $db->prepare($sql);
      $stmt->execute([$user_id, $business_name, $contact_number, $ein, $business_email, $website, $city, $state_id, $street_address, $zip_code, $description, $vendor_bio]);

      $_SESSION['message'] = "✅ Approval pending. Check your email for confirmation in the next 24-48h.";
      $redirect_url = "/vendor_dash.php";
    } else {
      $_SESSION['message'] = "✅ Account created successfully! Welcome to the site.";
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

// Handle User Login
if (is_post_request() && isset($_POST['login'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];

  if (is_blank($username) || is_blank($password)) {
    $_SESSION['message'] = "❌ Invalid username or password.";
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
      $_SESSION['message'] = "❌ Your account has been deactivated. Contact support.";
      header("Location: ../login.php");
      exit();
    }

    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['user_level_id'] = $user['user_level_id'];
    $_SESSION['profile_image'] = get_profile_image($user['user_id']);

    $_SESSION['message'] = "✅ Welcome back, " . h($user['username']) . "!";

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
    $_SESSION['message'] = "❌ Invalid username or password.";
    header("Location: ../login.php");
    exit();
  }
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
