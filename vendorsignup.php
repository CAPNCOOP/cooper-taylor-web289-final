<?php
$page_title = "Vendor Signup";
require_once 'private/initialize.php';
require_once 'private/config.php';
require_once 'private/header.php';
require_once 'private/functions.php';

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
  <h2>Vendor Sign Up</h2>

  <p>Becoming a vendor with us requires the submission of a valid Employer Identification Number (EIN) for verification. Once your EIN has been successfully approved, you will gain access to our platform, allowing you to register for upcoming market dates and showcase your products to our community. We strive to ensure a smooth and efficient approval process, but please allow up to 48 hours for confirmation. This step is essential for maintaining a high standard of service and ensuring all vendors meet our operational requirements.</p>

  <form action="private/auth.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="is_vendor" value="1">

    <div>
      <!-- Personal Info -->
      <fieldset>
        <label for="username" class="visually-hidden">Username</label>
        <input type="text" id="username" name="username" required placeholder="Username" value="<?= h($form_data['username'] ?? '') ?>">
      </fieldset>

      <fieldset>
        <label for="fname" class="visually-hidden">First Name</label>
        <input type="text" id="fname" name="fname" required placeholder="First Name" value="<?= h($form_data['fname'] ?? '') ?>">
      </fieldset>

      <fieldset>
        <label for="lname" class="visually-hidden">Last Name</label>
        <input type="text" id="lname" name="lname" required placeholder="Last Name" value="<?= h($form_data['lname'] ?? '') ?>">
      </fieldset>

      <fieldset>
        <label for="email" class="visually-hidden">Email Address</label>
        <input type="email" id="email" name="email" required placeholder="Email Address" value="<?= h($form_data['email'] ?? '') ?>">
      </fieldset>

      <fieldset>
        <label for="password" class="visually-hidden">Password</label>
        <input type="password" id="password" name="password" required placeholder="Password">
      </fieldset>

      <fieldset>
        <label for="confirm-pass" class="visually-hidden">Confirm Password</label>
        <input type="password" id="confirm-pass" name="confirm-pass" required placeholder="Confirm Password">
      </fieldset>

      <fieldset>
        <label for="business-name" class="visually-hidden">Business Name</label>
        <input type="text" id="business-name" name="business_name" required placeholder="Business Name" value="<?= h($form_data['business_name'] ?? '') ?>">
      </fieldset>

      <fieldset>
        <label for="contact-number" class="visually-hidden">Contact Number</label>
        <input type="text" id="contact-number" name="contact_number" required placeholder="Contact Number" value="<?= h($form_data['contact_number'] ?? '') ?>">
      </fieldset>

      <fieldset>
        <label for="business-ein" class="visually-hidden">Business EIN</label>
        <input type="text" id="business-ein" name="business_EIN" required placeholder="Business EIN" value="<?= h($form_data['business_EIN'] ?? '') ?>">
      </fieldset>
    </div>

    <div>
      <fieldset>
        <label for="business-email" class="visually-hidden">Business Email</label>
        <input type="email" id="business-email" name="business_email" required placeholder="Business Email" value="<?= h($form_data['business_email'] ?? '') ?>">
      </fieldset>

      <fieldset>
        <label for="website" class="visually-hidden">Website</label>
        <input
          type="url"
          id="website"
          name="website"
          placeholder="Website (optional)"
          pattern="https?://.+"
          value="<?= h($form_data['website'] ?? '') ?>">

      </fieldset>

      <fieldset>
        <label for="city" class="visually-hidden">City</label>
        <input type="text" id="city" name="city" required placeholder="City" value="<?= h($form_data['city'] ?? '') ?>">
      </fieldset>

      <fieldset>
        <label for="state" class="visually-hidden">State</label>
        <select id="state" name="state_id" required>
          <option value="">Select State</option>
          <?php
          $states = Admin::fetchStates();
          foreach ($states as $state) {
            $selected = ($form_data['state_id'] ?? '') == $state['state_id'] ? ' selected' : '';
            echo "<option value=\"" . h($state['state_id']) . "\"$selected>" . h($state['state_abbr']) . "</option>";
          }
          ?>
        </select>
      </fieldset>

      <fieldset>
        <label for="street-address" class="visually-hidden">Street Address</label>
        <input type="text" id="street-address" name="street_address" required placeholder="Street Address" value="<?= h($form_data['street_address'] ?? '') ?>">
      </fieldset>

      <fieldset>
        <label for="zip-code" class="visually-hidden">Zip Code</label>
        <input type="text" id="zip-code" name="zip_code" required placeholder="Zip Code" value="<?= h($form_data['zip_code'] ?? '') ?>">
      </fieldset>

      <fieldset>
        <label for="description" class="visually-hidden">Business Description</label>
        <textarea id="description" name="description" required placeholder="A short description of your business.."><?= h($form_data['description'] ?? '') ?></textarea>
      </fieldset>

      <fieldset>
        <label for="vendor-bio" class="visually-hidden">Vendor Bio</label>
        <textarea id="vendor-bio" name="vendor_bio" required placeholder="Tell us your story, values, or mission..."><?= h($form_data['vendor_bio'] ?? '') ?></textarea>
      </fieldset>
    </div>

    <div>
      <fieldset>
        <legend>Upload Profile Photo</legend>

        <div class="image-upload-wrapper">
          <img
            src="img/assets/add-photo.svg"
            alt="Add a vendor profile photo"
            id="profile-preview"
            class="image-preview"
            height="300"
            width="300"
            loading="lazy">

          <label for="user-profile-pic" class="upload-label" aria-label="Upload Vendor Profile Photo">
            Choose Photo
          </label>

          <input
            type="file"
            id="user-profile-pic"
            name="profile_image"
            class="image-input"
            accept="image/png, image/jpeg, image/webp"
            aria-describedby="profile-desc"
            onchange="previewImage(event)">

          <p id="profile-desc" class="visually-hidden">
            Upload a JPG, PNG, or WebP vendor profile photo.
          </p>
        </div>
      </fieldset>

      <div id="cropper-modal" style="display: none;">
        <div id="cropper-modal-inner">
          <img id="cropper-image" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==" alt="">
        </div>
        <button type="button" id="crop-confirm">Crop & Upload</button>
      </div>

      <input type="hidden" name="cropped-profile" id="cropped-image">

      <fieldset>
        <div class="g-recaptcha" data-sitekey="<?= RECAPTCHA_SITE_KEY ?>"></div>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <p class="visually-hidden">Please complete the CAPTCHA before submitting.</p>
        <button class="signup-button" type="submit" name="register" value="1">Sign Up</button>
      </fieldset>
    </div>
  </form>
</main>

<?php require_once 'private/footer.php'; ?>
