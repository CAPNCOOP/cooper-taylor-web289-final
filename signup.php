<?php
$page_title = "Sign Up";
require_once 'private/initialize.php';
require_once 'private/config.php';
require_once 'private/header.php';
require_once 'private/popup_message.php';

if ($session->is_logged_in()) {
  switch ($_SESSION['user_level_id']) {
    case 1: // member
      redirect_to('dashboard.php');
      break;
    case 2: // vendor
      redirect_to('vendor_dash.php');
      break;
    case 3: // admin
      redirect_to('admin_dash.php');
      break;
    case 4: // super admin
      redirect_to('superadmin_dash.php');
      break;
    default:
      redirect_to('index.php');
  }
}

$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
?>

<main role="main">
  <form action="private/auth.php" method="POST" enctype="multipart/form-data">

    <div>
      <legend>User Sign up</legend>

      <fieldset>
        <label for="username" class="visually-hidden">Username</label>
        <input type="text" id="username" name="username" placeholder="Username"
          value="<?= h($form_data['username'] ?? '') ?>" required>
      </fieldset>

      <fieldset>
        <label for="fname" class="visually-hidden">First Name</label>
        <input type="text" id="fname" name="fname" placeholder="First Name"
          value="<?= h($form_data['fname'] ?? '') ?>" required>
      </fieldset>

      <fieldset>
        <label for="lname" class="visually-hidden">Last Name</label>
        <input type="text" id="lname" name="lname" placeholder="Last Name"
          value="<?= h($form_data['lname'] ?? '') ?>" required>
      </fieldset>

      <fieldset>
        <label for="email" class="visually-hidden">Email Address</label>
        <input type="email" id="email" name="email" placeholder="Email Address"
          value="<?= h($form_data['email'] ?? '') ?>" required>
      </fieldset>

      <fieldset>
        <label for="password" class="visually-hidden">Password</label>
        <input type="password" id="password" name="password" placeholder="Password" required>
      </fieldset>

      <fieldset>
        <label for="confirm-pass" class="visually-hidden">Confirm Password</label>
        <input type="password" id="confirm-pass" name="confirm-pass" placeholder="Confirm Password" required>
      </fieldset>
    </div>

    <div>
      <fieldset>
        <label class="upload-label" role="button" for="user-profile-pic" aria-label="Upload Profile Photo">
          Upload Profile Photo
          <img
            src="img/assets/add-photo.svg"
            alt=""
            id="profile-preview"
            class="image-preview"
            width="250"
            height="250"
            loading="lazy" />
          <input
            type="file"
            name="profile_image"
            id="user-profile-pic"
            accept="image/png, image/jpeg, image/webp"
            aria-describedby="profile-desc"
            onchange="previewImage(event)"
            style="display: none;" />
        </label>
        <p id="profile-desc" class="visually-hidden">Upload a JPG, PNG, or WebP profile photo.</p>
      </fieldset>

      <!-- Cropping Modal -->
      <div id="cropper-modal" style="display: none;">
        <div id="cropper-modal-inner">
          <img id="cropper-image" src="">
        </div>
        <button type="button" id="crop-confirm">Crop & Upload</button>
      </div>

      <input type="hidden" name="cropped-image" id="cropped-image">

      <div>
        <div class="g-recaptcha" data-sitekey="<?= RECAPTCHA_SITE_KEY ?>"></div>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <button class="signup-button" type="submit" name="register" value="1">Sign Up</button>
      </div>
    </div>

  </form>
</main>

<?php require_once 'private/footer.php'; ?>
