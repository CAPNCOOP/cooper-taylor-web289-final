<?php
$page_title = "Log In";
require_once 'private/initialize.php';
require_once 'private/header.php';
?>

<main>
  <?php if (isset($_GET['error'])): ?>
    <?php if ($_GET['error'] === "invalid_credentials"): ?>
      <p class="error-message">Invalid username or password.</p>
    <?php elseif ($_GET['error'] === "account_inactive"): ?>
      <p class="error-message">Your account has been deactivated. Contact support.</p>
    <?php endif; ?>
  <?php endif; ?>

  <?php if (isset($_GET['signup_success'])): ?>
    <p class="success-message">Account created successfully! Pending approval.</p>
  <?php endif; ?>

  <form action="private/auth.php" method="POST" class="user-login">
    <legend>Log In</legend>

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

    <img src="/img/upload/users/default.png" alt="A stylized user icon." height="250" width="250">
    <fieldset>
      <label for="login-username"></label>
      <input type="text" id="login-username" name="username" placeholder="Username" aria-label="Username" required>
    </fieldset>

    <fieldset>
      <label for="login-password"></label>
      <input type="password" id="login-password" name="password" placeholder="Password" aria-label="Password" required>
    </fieldset>

    <button type="submit" name="login" value="1">Log In</button>
  </form>
</main>

<?php require_once 'private/footer.php'; ?>
