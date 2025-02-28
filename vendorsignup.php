<?php
require_once 'private/initialize.php';
require_once 'private/header.php';
$page_title = "Vendor - Signup"; // Set dynamic title
?>

<main>
  <form action="private/auth.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="is_vendor" value="1">

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
      <label for="email">Email:</label>
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
      <label for="contact-number">Contact Number:</label>
      <input type="text" id="contact-number" name="contact_number" required>
    </fieldset>

    <fieldset>
      <label for="business-ein">Business EIN:</label>
      <input type="text" id="business-ein" name="business_EIN" required>
    </fieldset>

    <fieldset>
      <label for="business-email">Business Email:</label>
      <input type="email" id="business-email" name="business_email" required>
    </fieldset>

    <fieldset>
      <label for="website">Website (optional):</label>
      <input type="url" id="website" name="website">
    </fieldset>

    <fieldset>
      <label for="city">City:</label>
      <input type="text" id="city" name="city" required>
    </fieldset>

    <fieldset>
      <label for="state">State:</label>
      <select id="state" name="state_id" required>
        <option value="">Select State</option>
        <?php
        // Fetch states from the database
        $state_sql = "SELECT state_id, state_abbr FROM state ORDER BY state_abbr ASC";
        $state_stmt = $db->prepare($state_sql);
        $state_stmt->execute();
        $states = $state_stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($states as $state) {
          echo "<option value=\"" . $state['state_id'] . "\">" . htmlspecialchars($state['state_abbr']) . "</option>";
        }
        ?>
      </select>
    </fieldset>

    <fieldset>
      <label for="street-address">Street Address:</label>
      <input type="text" id="street-address" name="street_address" required>
    </fieldset>

    <fieldset>
      <label for="zip-code">Zip Code:</label>
      <input type="text" id="zip-code" name="zip_code" required>
    </fieldset>

    <fieldset>
      <label for="description">Business Description:</label>
      <textarea id="description" name="description" required></textarea>
    </fieldset>

    <fieldset>
      <label for="vendor-bio">Vendor Bio:</label>
      <textarea id="vendor-bio" name="vendor_bio" required></textarea>
    </fieldset>

    <fieldset>
      <label for="profile-pic">Profile Picture:</label>
      <input type="file" id="profile-pic" name="profile_image" accept="image/png, image/jpeg, image/webp">
    </fieldset>

    <button type="submit" name="register" value="1">Sign Up</button>
  </form>
</main>

</body>

</html>
