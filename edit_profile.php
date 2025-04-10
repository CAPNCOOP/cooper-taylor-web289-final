<?php
$page_title = "Edit Profile Details";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_once 'private/functions.php';
require_login();

$user_id = $_SESSION['user_id'] ?? null;
$user_level = $_SESSION['user_level_id'] ?? null;

if (!$user_id) {
  die("❌ Unauthorized Access");
}

$user = User::find_by_id($user_id);
$profile_image = get_profile_image($user_id);

$vendor = null;
if ($user_level == 2) {
  $vendor = Vendor::find_by_user_id($user_id); // Make sure this method exists in Vendor class
}
?>

<main>
  <?php require_once 'private/popup_message.php'; ?>

  <?php if (isset($_GET['message'])): ?>
    <div class="feedback-popup">
      <?php
      switch ($_GET['message']) {
        case 'profile_updated':
          echo "✅ Profile updated successfully!";
          break;
        case 'error_update_failed':
          echo "❌ Error: Profile update failed.";
          break;
        case 'error_invalid_image':
          echo "❌ Error: Invalid image format.";
          break;
      }
      ?>
    </div>
  <?php endif; ?>

  <h2>Edit Profile</h2>
  <form action="process_profile_update.php" method="POST" enctype="multipart/form-data">
    <fieldset>
      <label for="profile_image">Profile Picture</label>
      <p><strong>Current:</strong></p>
      <img src="<?= url_for($profile_image) ?>" id="profile_preview" height="300" width="300" alt="Current Profile Picture.">
      <input type="file" id="profile_image" name="profile_image" data-preview="profile_preview" accept="image/png, image/jpeg, image/webp" onchange="previewImage(event)">
    </fieldset>

    <fieldset>
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" value="<?= h($user->username) ?>" required>
    </fieldset>

    <fieldset>
      <label for="email">Email:</label>
      <input type="email" id="email" name="email" value="<?= h($user->email) ?>" required>
    </fieldset>

    <?php if ($user_level == 2 && $vendor): ?>
      <fieldset>
        <label for="business_name">Business Name:</label>
        <input type="text" id="business_name" name="business_name" value="<?= h($vendor->business_name) ?>">
      </fieldset>

      <fieldset>
        <label for="business_ein">EIN:</label>
        <input type="text" id="business_ein" name="business_ein" value="<?= h($vendor->business_EIN) ?>">
      </fieldset>

      <fieldset>
        <label for="contact_number">Contact Number:</label>
        <input type="text" id="contact_number" name="contact_number" value="<?= h($vendor->contact_number) ?>">
      </fieldset>

      <fieldset>
        <label for="description">Business Description:</label>
        <textarea id="description" name="description"><?= h($vendor->description) ?></textarea>
      </fieldset>
    <?php endif; ?>

    <button type="submit">Save Changes</button>
  </form>
</main>

<?php require_once 'private/footer.php'; ?>
