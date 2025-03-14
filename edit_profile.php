<?php
$page_title = "Edit Profile Details";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_login(); // Ensure only logged-in users access this

$user_id = $_SESSION['user_id'] ?? null;
$user_level = $_SESSION['user_level_id'] ?? null;

if (!$user_id) {
  header("Location: login.php?message=error_unauthorized");
  exit();
}

// Fetch user data (profile image included)
$sql = "SELECT u.username, u.email, COALESCE(pi.file_path, 'img/upload/users/default.png') AS profile_image
        FROM users u 
        LEFT JOIN profile_image pi ON u.user_id = pi.user_id 
        WHERE u.user_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
  header("Location: dashboard.php?message=error_user_not_found");
  exit();
}

// Fetch vendor-specific details only if the user is a vendor
$vendor = null;
if ($user_level == 2) {
  $sql = "SELECT business_name, business_EIN, contact_number, business_email, website, city, state_id, 
                 street_address, zip_code, description, vendor_bio 
          FROM vendor WHERE user_id = ?";
  $stmt = $db->prepare($sql);
  $stmt->execute([$user_id]);
  $vendor = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<main>
  <?php require_once 'private/popup_message.php'; ?>

  <h2>Edit Profile</h2>

  <form action="process_profile_update.php" method="POST" enctype="multipart/form-data">
    <fieldset>
      <label for="profile_image">Profile Picture</label>
      <p><strong>Current:</strong></p>
      <img src="<?= url_for($user['profile_image']) ?>" height="300" width="300" alt="Profile Picture">
      <input type="file" id="profile_image" name="profile_image" accept="image/png, image/jpeg, image/webp">
    </fieldset>

    <fieldset>
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
    </fieldset>

    <fieldset>
      <label for="email">Email:</label>
      <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
    </fieldset>

    <?php if ($user_level == 2): // Vendor-specific fields 
    ?>
      <fieldset>
        <label for="business_name">Business Name:</label>
        <input type="text" id="business_name" name="business_name" value="<?= htmlspecialchars($vendor['business_name'] ?? '') ?>">
      </fieldset>

      <fieldset>
        <label for="business_ein">EIN:</label>
        <input type="text" id="business_ein" name="business_ein" value="<?= htmlspecialchars($vendor['business_EIN'] ?? '') ?>">
      </fieldset>

      <fieldset>
        <label for="contact_number">Contact Number:</label>
        <input type="text" id="contact_number" name="contact_number" value="<?= htmlspecialchars($vendor['contact_number'] ?? '') ?>">
      </fieldset>

      <fieldset>
        <label for="description">Business Description:</label>
        <textarea id="description" name="description"><?= htmlspecialchars($vendor['description'] ?? '') ?></textarea>
      </fieldset>
    <?php endif; ?>

    <button type="submit">Save Changes</button>
  </form>
</main>

<?php require_once 'private/footer.php'; ?>
