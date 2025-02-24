<?php
require_once 'private/initialize.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Blue Ridge Bounty - Log In</title>
  <link rel="stylesheet" href="css/styles.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="js/script.js" defer></script>
</head>

<body class="login-page">
  <header>
    <h1>Blue Ridge Bounty</h1>
    <nav>
      <ul>
        <li><a href="index.php"><img src="img/assets/barn.png" alt="An icon of a barn" height="25" width="25"></a></li>
        <li><a href="schedule.php">Schedule</a></li>
        <li><a href="ourvendors.php">Our Vendors</a></li>
        <li><a href="aboutus.php">About Us</a></li>
        <li><a href="login.php"><img src="img/assets/user.png" alt="A user login icon." height="25" width="25"></a></li>
      </ul>
    </nav>
  </header>

  <main>
    <?php if (isset($_GET['error'])): ?>
      <p class="error-message">Invalid username or password.</p>
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
        <label for="login-username">
        </label>
        <input type="text" id="login-username" name="username" placeholder="Username" aria-label="Username" required>
      </fieldset>

      <fieldset>
        <label for="login-password">
        </label>
        <input type="password" id="login-password" name="password" placeholder="Password" aria-label="Password" required>
      </fieldset>

      <button type="submit">Log In</button>
    </form>
  </main>
  <footer>
    <span>Blue Ridge Bounty &copy; 2025</span>
    <a href="aboutus.php#contact">Contact Us</a>
  </footer>
</body>

</html>
