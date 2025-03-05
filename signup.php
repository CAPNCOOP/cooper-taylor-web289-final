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

    <div>
      <legend>User Sign up</legend>
      <fieldset>
        <input type="text" id="username" name="username" required aria-label="Username" placeholder="Username">
      </fieldset>

      <fieldset>
        <input type="text" id="fname" name="fname" required aria-label="First Name" placeholder="First Name">
      </fieldset>

      <fieldset>
        <input type="text" id="lname" name="lname" required aria-label="Last Name" placeholder="Last Name">
      </fieldset>

      <fieldset>
        <input type="email" id="email" name="email" required aria-label="Email Address" placeholder="Email Address">
      </fieldset>

      <fieldset>
        <input type="password" id="password" name="password" required aria-label="Password" placeholder="Password">
      </fieldset>

      <fieldset>
        <input type="password" id="confirm-pass" name="confirm-pass" required aria-label="Confirm Password" placeholder="Confirm Password">
      </fieldset>
    </div>

    <div>
      <fieldset>
        <label for="profile-pic">Profile Picture:</label>
        <input type="file" id="profile-pic" name="profile_image" accept="image/png, image/jpeg, image/webp" required aria-label="Profile Picture">
        <img id="image-preview" src="" alt="Image Preview">
      </fieldset>
      <button type="submit" name="register" value="1">Sign Up</button>
    </div>

  </form>
</main>

<?php require_once 'private/footer.php'; ?>
