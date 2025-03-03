<?php
$page_title = "Sign Up";
ob_start(); // Start output buffering
require_once 'private/initialize.php';
require_once 'private/header.php';
ob_end_flush(); // End output buffering
?>

<main>
  <form action="private/auth.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="register" value="0">

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

    <button type="submit" name="register" value="1">Sign Up</button>
  </form>

  <?php require_once 'private/footer.php'; ?>
