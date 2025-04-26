<?php
$page_title = "Edit Profile Details";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_once 'private/functions.php';
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
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
  $vendor = Vendor::find_by_user_id($user_id);
}
?>

<main role="main">
  <?php require_once 'private/popup_message.php'; ?>

  <h2>Edit Profile</h2>
  <form action="process_profile_update.php" method="POST" enctype="multipart/form-data">
    <fieldset>
      <legend>Upload Profile Photo</legend>

      <div class="image-upload-wrapper">
        <img
          class="image-preview"
          id="profile-preview"
          src="<?= h('img/upload/' . $user->getImagePath()) ?>"
          alt="Current profile photo"
          height="300"
          width="300"
          loading="lazy">

        <label for="user-profile-pic" class="upload-label" aria-label="Upload New Profile Photo">
          Choose New Photo
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
          Upload a JPG, PNG, or WebP profile photo.
        </p>
      </div>
    </fieldset>

    <fieldset>
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" value="<?= h($form_data['username'] ?? $user->username) ?>" required>
    </fieldset>

    <fieldset>
      <label for="email">Email:</label>
      <input type="email" id="email" name="email" value="<?= h($form_data['email'] ?? $user->email) ?>" required>
    </fieldset>

    <?php if (Session::is_vendor() && $vendor): ?>
      <fieldset>
        <label for="business_name">Business Name:</label>
        <input type="text" id="business_name" name="business_name" value="<?= h($form_data['business_name'] ?? $vendor->business_name) ?>">
      </fieldset>

      <fieldset>
        <label for="business_ein">Business EIN:</label>
        <input type="text" id="business_ein" name="business_ein" value="<?= h($form_data['business_ein'] ?? $vendor->business_EIN) ?>">
      </fieldset>

      <fieldset>
        <label for="contact_number">Contact Number:</label>
        <input type="text" id="contact_number" name="contact_number" value="<?= h($form_data['contact_number'] ?? $vendor->contact_number) ?>">
      </fieldset>

      <fieldset>
        <label for="description">Business Description:</label>
        <textarea id="description" name="description" placeholder="A short description of your business.."><?= h($form_data['description'] ?? $vendor->description) ?></textarea>
      </fieldset>

      <fieldset>
        <label for="vendor_bio">Business Bio:</label>
        <textarea
          id="vendor-bio"
          name="vendor_bio"
          rows="6"
          placeholder="Tell us your story, values, or mission..."><?= h($form_data['vendor_bio'] ?? $vendor->vendor_bio) ?></textarea>
      </fieldset>
    <?php endif; ?>

    <fieldset>
      <label for="new_password">New Password</label>
      <input
        type="password"
        name="new_password"
        id="new_password"
        minlength="6"
        placeholder="Leave blank to keep current password">
    </fieldset>

    <fieldset>
      <label for="confirm_password">Confirm New Password</label>
      <input
        type="password"
        name="confirm_password"
        id="confirm_password"
        minlength="6"
        placeholder="Retype new password">
    </fieldset>

    <button type="submit">Save Changes</button>
  </form>
</main>

<div id="cropper-modal" style="display: none;">
  <div id="cropper-modal-inner">
    <img id="cropper-image" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==" alt="Image Preview Area">
  </div>
  <button type="button" id="crop-confirm">Crop & Upload</button>
</div>

<input type="hidden" name="cropped-profile" id="cropped-image">

<?php require_once 'private/footer.php'; ?>
