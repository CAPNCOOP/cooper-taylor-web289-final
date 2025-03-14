<?php
$page_title = "Log In";
require_once 'private/initialize.php';
require_once 'private/header.php';

// 🚀 Redirect if already logged in
if (isset($_SESSION['user_id'])) {
  header("Location: dashboard.php");
  exit;
}
?>

<main>
  <?php require_once 'private/popup_message.php'; ?>

  <form action="private/auth.php" method="POST" class="user-login">
    <legend>Log In</legend>

    <p>Not a user? <a href="#" id="openSignup">Sign up now!</a></p>

    <div id="signup-popup" class="popup hidden">
      <div class="popup-content">
        <h2>Sign Up</h2>
        <p>Choose your account type:</p>
        <button type="button" onclick="window.location.href='signup.php'">Member Signup</button>
        <button type="button" onclick="window.location.href='vendorsignup.php'">Vendor Signup</button>
        <button type="button" id="closeSignup">Cancel</button>
      </div>
    </div>

    <!-- CSRF Protection (Security Best Practice) -->
    <input type="hidden" name="token" value="<?= $_SESSION['token'] ?? '' ?>">

    <img src="/img/upload/users/default.png" alt="User Icon" height="250" width="250">

    <fieldset>
      <label for="login-username">Username</label>
      <input type="text" id="login-username" name="username" placeholder="Enter your username" aria-label="Username" required>
    </fieldset>

    <fieldset>
      <label for="login-password">Password</label>
      <input type="password" id="login-password" name="password" placeholder="Enter your password" aria-label="Password" required>
    </fieldset>

    <button type="submit" name="login" value="1" id="login">Log In</button>
  </form>
</main>

<?php require_once 'private/footer.php'; ?>
