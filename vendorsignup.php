<?php
$page_title = "Vendor Signup";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_once 'private/functions.php';
?>

<main>
  <h2>Vendor Sign Up</h2>

  <p>Becoming a vendor with us requires the submission of a valid Employer Identification Number (EIN) for verification. Once your EIN has been successfully approved, you will gain access to our platform, allowing you to register for upcoming market dates and showcase your products to our community. We strive to ensure a smooth and efficient approval process, but please allow up to 48 hours for confirmation. This step is essential for maintaining a high standard of service and ensuring all vendors meet our operational requirements.</p>

  <form action="private/auth.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="is_vendor" value="1">

    <!-- Personal Info -->
    <div>

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

      <fieldset>
        <input type="text" id="business-name" name="business_name" required aria-label="Business Name" placeholder="Business Name">
      </fieldset>

      <fieldset>
        <input type="text" id="contact-number" name="contact_number" required aria-label="Contact Number" placeholder="Contact Number">
      </fieldset>

      <fieldset>
        <input type="text" id="business-ein" name="business_EIN" required aria-label="Business EIN" placeholder="Business EIN">
      </fieldset>
    </div>

    <div>
      <fieldset>
        <input type="email" id="business-email" name="business_email" required aria-label="Business Email" placeholder="Business Email">
      </fieldset>

      <fieldset>
        <input type="url" id="website" name="website" aria-label="Website" placeholder="Website (optional)">
      </fieldset>

      <fieldset>
        <input type="text" id="city" name="city" required aria-label="City" placeholder="City">
      </fieldset>

      <fieldset>
        <select id="state" name="state_id" required aria-label="State">
          <option value="">Select State</option>
          <?php
          // Fetch states from the database
          $states = Admin::fetchStates();
          foreach ($states as $state) {
            echo "<option value=\"" . $state['state_id'] . "\">" . h($state['state_abbr']) . "</option>";
          }
          ?>
        </select>
      </fieldset>

      <fieldset>
        <input type="text" id="street-address" name="street_address" required aria-label="Street Address" placeholder="Street Address">
      </fieldset>

      <fieldset>
        <input type="text" id="zip-code" name="zip_code" required aria-label="Zip Code" placeholder="Zip Code">
      </fieldset>

      <div>
        <fieldset>
          <textarea id="description" name="description" required aria-label="Business Description" placeholder="Business Description, a short blurb about your business."></textarea>
        </fieldset>

        <fieldset>
          <textarea id="vendor-bio" name="vendor_bio" required aria-label="Vendor Bio" placeholder="Vendor Bio, tell the consumer about the history of your business!"></textarea>
        </fieldset>
      </div>

    </div>

    <div>

      <div>
        <fieldset>
          <label for="profile-pic">Choose File</label>
          <img id="profile-preview" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="temporary hidden image." height="300" width="300">
          <input type="file" id="profile-pic" data-preview="profile-preview" name="profile-pic" accept="image/png, image/jpeg, image/webp" aria-label="Profile Picture" onchange="previewImage(event)" required>
        </fieldset>
        <div>
          <button class="signup-button" type="submit" name="register" value="1">Sign Up</button>
        </div>
      </div>
    </div>
  </form>
</main>

<?php require_once 'private/footer.php'; ?>
