<?php
$page_title = "Sign Up";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_once 'private/popup_message.php';
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
?>

<main role="main">
  <form action="private/auth.php" method="POST" enctype="multipart/form-data" role="form">

    <div>
      <legend>User Sign up</legend>
      <fieldset>
        <input type="text" id="username" name="username" aria-label="Username" placeholder="Username"
          value="<?= h($form_data['username'] ?? '') ?>" required>
      </fieldset>

      <fieldset>
        <input type="text" id="fname" name="fname" aria-label="First Name" placeholder="First Name"
          value="<?= h($form_data['fname'] ?? '') ?>" required>
      </fieldset>

      <fieldset>
        <input type="text" id="lname" name="lname" aria-label="Last Name" placeholder="Last Name"
          value="<?= h($form_data['lname'] ?? '') ?>" required>
      </fieldset>

      <fieldset>
        <input type="email" id="email" name="email" aria-label="Email Address" placeholder="Email Address"
          value="<?= h($form_data['email'] ?? '') ?>" required>
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
          width="300"
          loading="lazy">

        <input type="file"
          id="user-profile-pic"
          name="profile-pic"
          class="image-input"
          data-preview="image-preview"
          accept="image/png, image/jpeg, image/webp"
          onchange="previewImage(event)">
      </fieldset>

      <div>
        <div class="g-recaptcha" data-sitekey="6Le47BgrAAAAACvegE-N7BsAVv3Bo6dvcd6Cj0tU"></div>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <button class="signup-button" type="submit" name="register" value="1" aria-label="Sign Up">Sign Up</button>
      </div>
    </div>

  </form>
</main>

<?php require_once 'private/footer.php'; ?>
