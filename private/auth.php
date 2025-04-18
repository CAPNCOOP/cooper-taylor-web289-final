<?php
require_once 'initialize.php';
require_once 'validation_functions.php';
require_once 'functions.php';
require_once 'config.php';
require_once 'classes/User.class.php';
require_once 'classes/Vendor.class.php';

$errors = [];
$profile_image = 'img/upload/users/default.png';

// Register /////////////////////////////////////////////////////////
if (is_post_request() && isset($_POST['register']) && $_POST['register'] == '1') {

  $form_origin = $_SERVER['HTTP_REFERER'] ?? '../signup.php';


  $admin_created = isset($_POST['admin_created']) && $_POST['admin_created'] == '1';
  $is_vendor = isset($_POST['is_vendor']) ? (int)$_POST['is_vendor'] : 0;

  // Collect fields
  $username = strtolower(trim($_POST['username'] ?? ''));

  // CAPTCHA Validation
  $recaptcha_secret = RECAPTCHA_SECRET_KEY;
  $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

  // Require the CAPTCHA response
  if (empty($recaptcha_response)) {
    $_SESSION['form_data'] = $_POST;
    $session->message("❌ Please complete the CAPTCHA before submitting.");
    redirect_to($form_origin);
    exit();
  }

  // Verify the CAPTCHA response with Google
  $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptcha_secret}&response={$recaptcha_response}");
  $captcha_result = json_decode($verify);

  if (!$captcha_result->success) {
    $session->message("❌ CAPTCHA verification failed. Please try again.");
    redirect_to('../signup.php');
    exit(); // Stop execution immediately
  }

  // If we got here, CAPTCHA is valid - continue with registration

  $fname = trim($_POST['fname'] ?? '');
  $lname = trim($_POST['lname'] ?? '');
  $email = strtolower(trim($_POST['email'] ?? ''));
  $password = $_POST['password'] ?? '';
  $confirm_password = $_POST['confirm-pass'] ?? '';
  $ein = $is_vendor ? $_POST['business_EIN'] ?? '' : NULL;

  // Validation
  if (is_blank($username) || is_blank($email) || is_blank($password)) {
    $_SESSION['form_data'] = $_POST;
    $session->message("❌ Required fields cannot be blank.");
    redirect_to($form_origin);
    exit();
  }
  if (!has_length($password, ['min' => 8])) {
    $_SESSION['form_data'] = $_POST;
    $session->message("❌ Password must be at least 8 characters.");
    redirect_to($form_origin);
    exit();
  }
  if ($password !== $confirm_password) {
    $_SESSION['form_data'] = $_POST;
    $session->message("❌ Passwords do not match.");
    redirect_to($form_origin);
    exit();
  }
  if (!has_valid_email_format($email)) {
    $_SESSION['form_data'] = $_POST;
    $session->message("❌ Invalid email format.");
    redirect_to($form_origin);
    exit();
  }
  if (!has_unique_username($username)) {
    $_SESSION['form_data'] = $_POST;
    $session->message("❌ Username already taken.");
    redirect_to($form_origin);
    exit();
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
  $clean_name = strtolower($fname . '_' . $lname);
  $uploaded_filename = null;

  $uploaded_filename = handle_cropped_upload('cropped-profile', 'users', $clean_name, $user_id);

  if (!$uploaded_filename && isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $uploaded_filename = upload_image($_FILES['profile_image'], 'users', $clean_name, $user_id);
  }

  $profile_image = $uploaded_filename
    ? "users/" . $uploaded_filename
    : 'img/upload/users/default.webp';

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
      redirect_to('../admin_dash.php');
    } else {
      redirect_to('../dashboard.php');
    }
    exit();
  }


  $session->login([
    'user_id' => $user_id,
    'username' => $username,
    'user_level' => $is_vendor ? 2 : 1
  ]);
  $_SESSION['profile_image'] = $profile_image;
  $_SESSION['user_level_id'] = $is_vendor ? 2 : 1;

  $session->message("✅ Account created successfully!");
  $redirect_url = $is_vendor ? "/vendor_dash.php" : "/dashboard.php";
  redirect_to($redirect_url);
}

// login ////////////////////////////////////////////////////////////////
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
    $_SESSION['breadcrumbs'] = [
      ['label' => 'Home', 'path' => url_for('/index.php')],
    ];

    $session->message("✅ Welcome back, " . h($user['username']) . "!");

    switch ($user['user_level_id']) {
      case 2:
        redirect_to('../vendor_dash.php');
        break;
      case 3:
        redirect_to('../admin_dash.php');
        break;
      case 4:
        redirect_to('../superadmin_dash.php');
        break;
      default:
        redirect_to('../dashboard.php');
        break;
    }
  } else {
    $session->message("❌ Invalid username or password.");
    redirect_to('../login.php');
  }
}
