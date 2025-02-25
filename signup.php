<?php
ob_start(); // Start output buffering
require_once 'private/initialize.php';
ob_end_flush(); // End output buffering
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

<body>
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
    <form action="private/auth.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="is_vendor" value="0">

      <fieldset>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
      </fieldset>

      <fieldset>
        <label for="fname">First Name:</label>
        <input type="text" id="fname" name="fname" required>
      </fieldset>

      <fieldset>
        <label for="lname">Last Name:</label>
        <input type="text" id="lname" name="lname" required>
      </fieldset>

      <fieldset>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
      </fieldset>

      <fieldset>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
      </fieldset>

      <fieldset>
        <label for="confirm-pass">Confirm Password:</label>
        <input type="password" id="confirm-pass" name="confirm-pass" required>
      </fieldset>

      <fieldset>
        <label for="profile-pic">Profile Picture:</label>
        <input type="file" id="profile-pic" name="profile_image" accept="image/png, image/jpeg, image/webp" required>
      </fieldset>

      <button type="submit">Sign Up</button>
    </form>
  </main>
</body>
