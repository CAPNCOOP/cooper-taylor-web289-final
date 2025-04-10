<?php
$page_title = "Member - Dashboard";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_once 'private/functions.php';
require_login();

$user_id = $_SESSION['user_id'] ?? null;

$user = User::find_by_id($user_id);
$user_type = ($user->user_level_id == 2) ? 'Vendor' : 'Member';

$profile_image = get_profile_image($user_id);

// Fetch saved vendors
$favorite = [];
if (Favorite::tableExists()) {
  $favorites = Favorite::fetchFavoritesForUser($user_id);
}
?>

<?php if ($session->message()): ?>
  <div class="message">
    <p><?= h($session->message()) ?></p>
  </div>
<?php $session->clear_message();
endif; ?>

<div id="user-profile">
  <div id="user-card">
    <h2>Hello, <strong><?= h($user->username) ?></strong>!</h2>
    <img src="<?= h($profile_image) ?>" alt="Profile Picture" height="250" width="250">
    <p><strong>Email:</strong> <?= h($user->email) ?></p>
    <p><strong>Account Type:</strong> <?= $user_type ?></p>
    <a href="edit_profile.php" class="btn"><img src="/img/assets/edit.png" width="40" height="40" alt="An edit icon.">Edit Details</a>
  </div>

  <div id="saved-vendors">
    <h2>Saved Vendors</h2>
    <?php if (!empty($favorites)): ?>
      <ul>
        <?php foreach ($favorites as $vendor): ?>
          <li>
            <img src="<?= h($vendor->profile_image ?? 'default.png') ?>" height="200" width="200" alt="An Image of a Vendor.">
            <a href="vendor_profile.php?vendor_id=<?= h($vendor->vendor_id) ?>">
              <?= h($vendor->business_name) ?>
            </a>
            <a href="remove_vendor.php?vendor_id=<?= h($vendor->vendor_id) ?>" class="remove-link">Remove</a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p>No saved vendors yet.</p>
    <?php endif; ?>
  </div>
</div>

<?php require_once 'private/footer.php'; ?>
