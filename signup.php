<?php
$page_title = "Sign Up";
require_once 'private/initialize.php';
require_once 'private/config.php';
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
        <label class="upload-label" tabindex="0" role="button" for="user-profile-pic" aria-label="Upload Profile Photo">
          Upload Profile Photo
          <img
            src="img/assets/add-photo.svg"
            alt="Profile Photo Preview"
            id="profile-preview"
            class="image-preview"
            width="200"
            height="200"
            loading="lazy" />
          <input
            type="file"
            name="profile_image"
            id="user-profile-pic"
            accept="image/png, image/jpeg, image/webp"
            onchange="previewImage(event)"
            style="display: none;" />
        </label>
      </fieldset>

      <!-- Cropping Modal -->
      <div id="cropper-modal" style="display: none;">
        <div id="cropper-modal-inner">
          <img id="cropper-image" src="">
        </div>
        <button type="button" id="crop-confirm">Crop & Upload</button>
      </div>

      <!-- Hidden input (Base64) for the final image -->
      <input type="hidden" name="cropped-image" id="cropped-image">

      <div>
        <div class="g-recaptcha" data-sitekey="<?= RECAPTCHA_SITE_KEY ?>"></div>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <button class="signup-button" type="submit" name="register" value="1" aria-label="Sign Up">Sign Up</button>
      </div>
    </div>

  </form>
</main>

<?php require_once 'private/footer.php'; ?>
