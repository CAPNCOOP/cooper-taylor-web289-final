<?php
$page_title = "Vendor Signup";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_once 'private/popup_message.php';
?>

<main>
  <h2>Vendor Sign Up</h2>

  <form action="private/auth.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="is_vendor" value="1">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

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
      <label for="email">Email Address:</label>
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
      <label for="business-name">Business Name:</label>
      <input type="text" id="business-name" name="business_name" required>
    </fieldset>

    <fieldset>
      <label for="business-ein">Business EIN (XX-XXXXXXX):</label>
      <input type="text" id="business-ein" name="business_EIN" required pattern="\d{2}-\d{7}">
    </fieldset>

    <fieldset>
      <label for="profile-pic">Profile Picture:</label>
      <input type="file" id="profile-pic" name="profile_image" accept="image/png, image/jpeg, image/webp" required onchange="previewImage()">
      <img id="image-preview" src="" alt="Image Preview" height="250" width="250">
    </fieldset>

    <button type="submit" name="register" value="1">Sign Up</button>
  </form>
</main>

<?php require_once 'private/footer.php'; ?>
