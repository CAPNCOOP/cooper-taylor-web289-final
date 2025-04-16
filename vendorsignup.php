<?php
$page_title = "Vendor Signup";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_once 'private/functions.php';

$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
?>

<main role="main">
  <h2>Vendor Sign Up</h2>

  <p>Becoming a vendor with us requires the submission of a valid Employer Identification Number (EIN) for verification. Once your EIN has been successfully approved, you will gain access to our platform, allowing you to register for upcoming market dates and showcase your products to our community. We strive to ensure a smooth and efficient approval process, but please allow up to 48 hours for confirmation. This step is essential for maintaining a high standard of service and ensuring all vendors meet our operational requirements.</p>

  <form action="private/auth.php" method="POST" enctype="multipart/form-data" role="form">
    <input type="hidden" name="is_vendor" value="1">

    <!-- Personal Info -->
    <div>
      <fieldset>
        <input type="text" id="username" name="username" required placeholder="Username"
          value="<?= h($form_data['username'] ?? '') ?>">
      </fieldset>

      <fieldset>
        <input type="text" id="fname" name="fname" required placeholder="First Name"
          value="<?= h($form_data['fname'] ?? '') ?>">
      </fieldset>

      <fieldset>
        <input type="text" id="lname" name="lname" required placeholder="Last Name"
          value="<?= h($form_data['lname'] ?? '') ?>">
      </fieldset>

      <fieldset>
        <input type="email" id="email" name="email" required placeholder="Email Address"
          value="<?= h($form_data['email'] ?? '') ?>">
      </fieldset>

      <fieldset>
        <input type="password" id="password" name="password" required placeholder="Password">
      </fieldset>

      <fieldset>
        <input type="password" id="confirm-pass" name="confirm-pass" required placeholder="Confirm Password">
      </fieldset>

      <fieldset>
        <input type="text" id="business-name" name="business_name" required placeholder="Business Name"
          value="<?= h($form_data['business_name'] ?? '') ?>">
      </fieldset>

      <fieldset>
        <input type="text" id="contact-number" name="contact_number" required placeholder="Contact Number"
          value="<?= h($form_data['contact_number'] ?? '') ?>">
      </fieldset>

      <fieldset>
        <input type="text" id="business-ein" name="business_EIN" required placeholder="Business EIN"
          value="<?= h($form_data['business_EIN'] ?? '') ?>">
      </fieldset>
    </div>

    <div>
      <fieldset>
        <input type="email" id="business-email" name="business_email" required placeholder="Business Email"
          value="<?= h($form_data['business_email'] ?? '') ?>">
      </fieldset>

      <fieldset>
        <input type="url" id="website" name="website" placeholder="Website (optional)"
          value="<?= h($form_data['website'] ?? '') ?>">
      </fieldset>

      <fieldset>
        <input type="text" id="city" name="city" required placeholder="City"
          value="<?= h($form_data['city'] ?? '') ?>">
      </fieldset>

      <fieldset>
        <select id="state" name="state_id" required>
          <option value="">Select State</option>
          <?php
          $states = Admin::fetchStates();
          foreach ($states as $state) {
            $selected = ($form_data['state_id'] ?? '') == $state['state_id'] ? 'selected' : '';
            echo "<option value=\"" . $state['state_id'] . "\" $selected>" . h($state['state_abbr']) . "</option>";
          }
          ?>
        </select>
      </fieldset>

      <fieldset>
        <input type="text" id="street-address" name="street_address" required placeholder="Street Address"
          value="<?= h($form_data['street_address'] ?? '') ?>">
      </fieldset>

      <fieldset>
        <input type="text" id="zip-code" name="zip_code" required placeholder="Zip Code"
          value="<?= h($form_data['zip_code'] ?? '') ?>">
      </fieldset>

      <div>
        <fieldset>
          <textarea id="description" name="description" required placeholder="Business Description"><?= h($form_data['description'] ?? '') ?></textarea>
        </fieldset>

        <fieldset>
          <textarea id="vendor-bio" name="vendor_bio" required placeholder="Vendor Bio"><?= h($form_data['vendor_bio'] ?? '') ?></textarea>
        </fieldset>
      </div>
    </div>
    <div>

      <div>
        <fieldset>
          <label for="vendor-profile-pic">Choose Profile Photo</label>

          <img class="image-preview"
            src="img/assets/add-photo.svg"
            alt="Vendor Profile Preview"
            data-preview="image-preview"
            height="300"
            width="300"
            loading="lazy">

          <input type="file"
            id="vendor-profile-pic"
            name="profile-pic"
            class="image-input"
            data-preview="image-preview"
            accept="image/png, image/jpeg, image/webp"
            onchange="previewImage(event)">
        </fieldset>


        <div>
          <div class="g-recaptcha" data-sitekey="6Le47BgrAAAAACvegE-N7BsAVv3Bo6dvcd6Cj0tU"></div>
          <script src="https://www.google.com/recaptcha/api.js" async defer></script>
          <button class="signup-button" type="submit" name="register" value="1">Sign Up</button>
        </div>
      </div>
    </div>
  </form>
</main>

<?php require_once 'private/footer.php'; ?>
