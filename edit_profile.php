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
  $vendor = Vendor::find_by_user_id($user_id); // Make sure this method exists in Vendor class
}
?>

<main role="main">
  <?php require_once 'private/popup_message.php'; ?>

  <h2>Edit Profile</h2>
  <form action="process_profile_update.php" method="POST" enctype="multipart/form-data" role="form">
    <fieldset>
      <label for="user-profile-pic">Choose Profile Photo</label>

      <img class="image-preview"
        id="profile-preview"
        src="<?= h('img/upload/' . $user->getImagePath()) ?>"
        alt="Current Profile Image"
        data-preview="image-preview"
        height="300"
        width="300"
        loading="lazy">

      <input type="file"
        id="user-profile-pic"
        name="profile_image"
        class="image-input"
        data-preview="image-preview"
        accept="image/png, image/jpeg, image/webp"
        onchange="previewImage(event)">

    </fieldset>

    <div id="cropper-modal" style="display: none;">
      <div id="cropper-modal-inner">
        <img id="cropper-image" src="">
      </div>
      <button type="button" id="crop-confirm">Crop & Upload</button>
    </div>

    <input type="hidden" name="cropped-profile" id="cropped-image">

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
        <input type="text" id="business_name" name="business_name"
          value="<?= h($form_data['business_name'] ?? $vendor->business_name) ?>">
      </fieldset>

      <fieldset>
        <label for="business_ein">Business EIN:</label>
        <input type="text" id="business_ein" name="business_ein"
          value="<?= h($form_data['business_ein'] ?? $vendor->business_EIN) ?>">
      </fieldset>

      <fieldset>
        <label for="contact_number">Contact Number:</label>
        <input type="text" id="contact_number" name="contact_number"
          value="<?= h($form_data['contact_number'] ?? $vendor->contact_number) ?>">
      </fieldset>

      <fieldset>
        <label for="description">Business Description:</label>
        <textarea id="description" name="description"><?= h($form_data['description'] ?? $vendor->description) ?></textarea>
      </fieldset>

    <?php endif; ?>

    <button type="submit" aria-label="Save Profile Changes">Save Changes</button>
  </form>
</main>

<?php require_once 'private/footer.php'; ?>
