<?php
$page_title = "Log In";
require_once 'private/initialize.php';
require_once 'private/header.php';

if ($session->is_logged_in()) {
  switch ($_SESSION['user_level_id']) {
    case 1: // member
      redirect_to('dashboard.php');
      break;
    case 2: // vendor
      redirect_to('vendor_dash.php');
      break;
    case 3: // admin
      redirect_to('admin_dash.php');
      break;
    case 4: // super admin
      redirect_to('superadmin_dash.php');
      break;
    default:
      redirect_to('index.php');
  }
}

?>

<main role="main">
  <?php require_once 'private/popup_message.php'; ?>

  <form action="private/auth.php" method="POST" class="user-login" role="form">
    <span>Log In</span>

    <p>Not a user? <a href="#" id="openSignup">Sign up now!</a></p>

    <div id="signup-popup" class="popup hidden">
      <div class="popup-content">
        <h2>Sign Up</h2>
        <p>Choose your account type:</p>
        <button onclick="window.location.href='signup.php'">Member Signup</button>
        <button onclick="window.location.href='vendorsignup.php'">Vendor Signup</button>
        <button id="closeSignup">Cancel</button>
      </div>
    </div>

    <input type="hidden" name="login" value="1">

    <img src="/img/upload/users/default.webp" alt="A stylized user icon." height="250" width="250" loading="lazy">
    <fieldset>
      <label for="login-username"></label>
      <input type="text" id="login-username" name="username" placeholder="Username" aria-label="Username" required>
    </fieldset>

    <fieldset>
      <label for="login-password"></label>
      <input type="password" id="login-password" name="password" placeholder="Password" aria-label="Password" required>
    </fieldset>

    <button type="submit" name="login" value="1" id="login">Log In</button>
  </form>
</main>

<?php require_once 'private/footer.php'; ?>
