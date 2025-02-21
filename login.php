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
      <input type="hidden" name="login" value="1">
      <fieldset>
        <label for="login-username">
          <img src="img/assets/user.png" alt="User icon" height="50" width="50">
        </label>
        <input type="text" id="login-username" name="username" placeholder="Username" aria-label="Username" required>
      </fieldset>

      <fieldset>
        <label for="login-password">
          <img src="img/assets/lock.png" alt="Lock icon" height="50" width="50">
        </label>
        <input type="password" id="login-password" name="password" placeholder="Password" aria-label="Password" required>
      </fieldset>

      <button type="submit">Log In</button>
    </form>

    <form action="private/auth.php" method="POST">
      <legend>New User? Create an account!</legend>
      <input type="hidden" name="register" value="1">

      <p>Items with * are required.</p>
      <fieldset>
        <label for="register-username">Username: </label>
        <input type="text" id="register-username" name="username" placeholder="*" required>
      </fieldset>
      <fieldset>
        <label for="register-fname">First Name: </label>
        <input type="text" id="register-fname" name="fname" placeholder="*" required>
      </fieldset>
      <fieldset>
        <label for="register-lname">Last Name: </label>
        <input type="text" id="register-lname" name="lname" placeholder="*" required>
      </fieldset>
      <fieldset>
        <label for="register-email">Email: </label>
        <input type="email" id="register-email" name="email" placeholder="*" required>
      </fieldset>
      <fieldset>
        <label for="register-password">Password: </label>
        <input type="password" id="register-password" name="password" placeholder="*" required>
      </fieldset>
      <fieldset>
        <label for="register-confirm-pass">Confirm Password: </label>
        <input type="password" id="register-confirm-pass" name="confirm-pass" placeholder="*" required>
      </fieldset>
      <fieldset>
        <label for="profile-pic">Profile Image: </label>
        <input type="file" id="profile-pic" name="profile-pic" required>
      </fieldset>
      <fieldset>
        <fieldset>
          Are you a vendor?
          <input type="radio" id="yes" value="yes" name="vendorRequest">
          <label for="yes">Yes</label>
          <input type="radio" id="no" value="no" name="vendorRequest">
          <label for="no">No</label>
        </fieldset>
        <fieldset class="ein-field">
          <label for="ein">EIN</label>
          <input type="text" name="ein" id="ein" placeholder="*">
        </fieldset>
      </fieldset>
      <button type="submit">Sign Up</button>
    </form>
  </main>
  <footer>
    <span>Blue Ridge Bounty &copy; 2025</span>
    <a href="aboutus.php#contact">Contact Us</a>
  </footer>
</body>

</html>
