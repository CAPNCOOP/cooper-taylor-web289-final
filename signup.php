<?php
$page_title = "Sign Up";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_once 'private/popup_message.php';
?>

<main>
  <form action="private/auth.php" method="POST" enctype="multipart/form-data">

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
        <label for="user-profile-pic">Choose Profile Photo</label>

        <img class="image-preview"
          src="img/assets/add-photo.svg"
          alt="User Profile Preview"
          data-preview="image-preview"
          height="300"
          width="300">

        <input type="file"
          id="user-profile-pic"
          name="profile-pic"
          class="image-input"
          data-preview="image-preview"
          accept="image/png, image/jpeg, image/webp"
          onchange="previewImage(event)">
      </fieldset>


      <div>
        <button class="signup-button" type="submit" name="register" value="1">Sign Up</button>
      </div>
    </div>

  </form>
</main>

<?php require_once 'private/footer.php'; ?>
