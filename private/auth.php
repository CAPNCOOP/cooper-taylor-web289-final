<?php
require_once 'initialize.php';
require_once 'validation_functions.php';
require_once 'functions.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}


$errors = [];

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
    $sql = "INSERT INTO users (username, first_name, last_name, email, password, user_level_id, business_EIN, user_status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')";
    $stmt = $db->prepare($sql);
    $stmt->execute([$username, $fname, $lname, $email, $hashed_password, $is_vendor ? 2 : 1, $ein]); // 2 for vendors, 1 for regular users


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
    $sql = "SELECT user_id, username, password, is_vendor, user_status FROM users WHERE username = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
      if ($user['user_status'] !== 'approved') {
        redirect_to('login.php?error=account_pending');
      }

      $_SESSION['user_id'] = $user['user_id'];
      $_SESSION['username'] = $user['username'];
      $_SESSION['is_vendor'] = $user['is_vendor'];

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
