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
        <input type="text" id="username" name="username" aria-label="Username" placeholder="Username" required>
      </fieldset>

      <fieldset>
        <input type="text" id="fname" name="fname" aria-label="First Name" placeholder="First Name" required>
      </fieldset>

      <fieldset>
        <input type="text" id="lname" name="lname" aria-label="Last Name" placeholder="Last Name" required>
      </fieldset>

      <fieldset>
        <input type="email" id="email" name="email" aria-label="Email Address" placeholder="Email Address" required>
      </fieldset>

      <fieldset>
        <input type="password" id="password" name="password" aria-label="Password" placeholder="Password" required>
      </fieldset>

      <fieldset>
        <input type="password" id="confirm-pass" name="confirm-pass" aria-label="Confirm Password" placeholder="Confirm Password" required>
      </fieldset>
    </div>

    <div>
      <fieldset>
        <label for="profile-pic">Choose File</label>
        <img id="profile-preview" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="" height="300" width="300">
        <input type="file" id="profile-pic" data-preview="profile-preview" name="profile-pic" accept="image/png, image/jpeg, image/webp" aria-label="Profile Picture" onchange="previewImage(event)" required>
      </fieldset>
      <div>
        <button class="signup-button" type="submit" name="register" value="1">Sign Up</button>
      </div>
    </div>

  </form>
</main>

<?php require_once 'private/footer.php'; ?>
