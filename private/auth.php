<?php
require_once 'initialize.php';
require_once 'validation_functions.php';
require_once 'functions.php';
require_once 'classes/User.class.php';
require_once 'classes/Vendor.class.php';

$errors = [];
$profile_image = 'img/upload/users/default.png';

if (is_post_request() && isset($_POST['register']) && $_POST['register'] == '1') {
  $admin_created = isset($_POST['admin_created']) && $_POST['admin_created'] == '1';
  $is_vendor = isset($_POST['is_vendor']) ? (int)$_POST['is_vendor'] : 0;

  // Collect fields
  $username = strtolower(trim($_POST['username'] ?? ''));
  $fname = trim($_POST['fname'] ?? '');
  $lname = trim($_POST['lname'] ?? '');
  $email = strtolower(trim($_POST['email'] ?? ''));
  $password = $_POST['password'] ?? '';
  $confirm_password = $_POST['confirm-pass'] ?? '';
  $ein = $is_vendor ? $_POST['business_EIN'] ?? '' : NULL;

  // Validation
  if (is_blank($username) || is_blank($email) || is_blank($password)) {
    $session->message("❌ Required fields cannot be blank.");
    redirect_to('../signup.php');
  }
  if (!has_length($password, ['min' => 8])) {
    $session->message("❌ Password must be at least 8 characters.");
    redirect_to('../signup.php');
  }
  if ($password !== $confirm_password) {
    $session->message("❌ Passwords do not match.");
    redirect_to('../signup.php');
  }
  if (!has_valid_email_format($email)) {
    $session->message("❌ Invalid email format.");
    redirect_to('../signup.php');
  }
  if (!has_unique_username($username)) {
    $session->message("❌ Username already taken.");
    redirect_to('../signup.php');
  }

  // Create user
  $hashed_password = password_hash($password, PASSWORD_BCRYPT);
  $user = new User([
    'username' => $username,
    'first_name' => $fname,
    'last_name' => $lname,
    'email' => $email,
    'password' => $hashed_password,
    'user_level_id' => $is_vendor ? 2 : 1
  ]);

  $result = $user->save();

  if (!$result) {
    $session->message("❌ Failed to create user.");
    redirect_to('../signup.php');
  }

  $user_id = $user->user_id;

  // Upload profile image
  $clean_name = strtolower($fname . '_' . $lname . '_' . $user_id);
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

  // Insert vendor info if applicable
  if ($is_vendor) {
    $vendor = new Vendor([
      'user_id' => $user_id,
      'business_name' => $_POST['business_name'],
      'contact_number' => $_POST['contact_number'],
      'business_EIN' => $ein,
      'business_email' => $_POST['business_email'],
      'website' => $_POST['website'],
      'city' => $_POST['city'],
      'state_id' => $_POST['state_id'],
      'street_address' => $_POST['street_address'],
      'zip_code' => $_POST['zip_code'],
      'description' => $_POST['description'],
      'vendor_bio' => $_POST['vendor_bio']
    ]);

    $vendor->user_id = $user->user_id;

    if (!$vendor->save()) {
      $session->message("❌ Vendor creation failed.");
      redirect_to('../superadmin_dash.php');
    }
  }

  if ($admin_created) {
    $session->message("✅ Vendor created successfully.");
    if ($_SESSION['user_level_id'] == 4) {
      redirect_to('../superadmin_dash.php');
    } elseif ($_SESSION['user_level_id'] == 3) {
      redirect_to('../admindash.php');
    } else {
      redirect_to('../dashboard.php');
    }
  }

  $session->login([
    'user_id' => $user_id,
    'username' => $username,
    'user_level' => $is_vendor ? 2 : 1
  ]);
  $_SESSION['profile_image'] = $profile_image;

  $session->message("✅ Account created successfully!");
  $redirect_url = $is_vendor ? "/vendor_dash.php" : "/dashboard.php";
  redirect_to($redirect_url);
}

if (is_post_request() && isset($_POST['login'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];

  if (is_blank($username) || is_blank($password)) {
    $session->message("❌ Invalid username or password.");
    redirect_to('../login.php');
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
      $session->message("❌ Your account has been deactivated. Contact support.");
      redirect_to('../login.php');
    }

    $session->login([
      'user_id' => $user['user_id'],
      'username' => $user['username'],
      'user_level' => $user['user_level_id']
    ]);
    $_SESSION['profile_image'] = get_profile_image($user['user_id']);
    $_SESSION['user_level_id'] = $user['user_level_id'];


    $session->message("✅ Welcome back, " . h($user['username']) . "!");
    error_log("Redirecting user level: " . $user['user_level_id']);

    switch ($user['user_level_id']) {
      case 2:
        error_log("Redirecting to vendor dashboard");
        redirect_to('../vendor_dash.php');
        break;
      case 3:
        error_log("Redirecting to admin dashboard");
        redirect_to('../admin_dash.php');
        break;
      case 4:
        error_log("Redirecting to SUPERADMIN dashboard");
        redirect_to('../superadmin_dash.php');
        break;
      default:
        error_log("Redirecting to generic dashboard");
        redirect_to('../dashboard.php');
        break;
    }
  } else {
    $session->message("❌ Invalid username or password.");
    redirect_to('../login.php');
  }
}
