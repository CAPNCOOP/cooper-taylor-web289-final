<?php
$page_title = "Member - Dashboard";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_once 'private/functions.php';
require_login();
Session::require_member();

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
<main role="main">
  <?php require_once 'private/popup_message.php'; ?>

  <div id="user-profile">
    <div id="user-card">
      <h2>Hello, <strong><?= h($user->username) ?></strong>!</h2>
      <img src="<?= h('img/upload/' . $user->getImagePath()) ?>" alt="Profile Picture" height="250" width="250" loading="lazy">
      <p><strong>Email:</strong> <?= h($user->email) ?></p>
      <p><strong>Account Type:</strong> <?= $user_type ?></p>
      <a href="edit_profile.php" class="btn"><img src="/img/assets/edit.png" width="40" height="40" alt="An edit icon." aria-label="Edit Profile Details">Edit Details</a>
    </div>

    <h2>Saved Vendors</h2>
    <div id="saved-vendors">
      <?php if (!empty($favorites)): ?>
        <ul>
          <?php foreach ($favorites as $vendor): ?>
            <li>
              <img src="<?= h('img/upload/' . $vendor->profile_image ?? 'default.png') ?>" height="200" width="200" alt="An Image of a Vendor." loading="lazy">
              <a href="vendor_profile.php?vendor_id=<?= h($vendor->vendor_id) ?>" aria-label="View Vendor Profile">
                <?= h($vendor->business_name) ?>
              </a>
              <a href="remove_vendor.php?vendor_id=<?= h($vendor->vendor_id) ?>" class="remove-link" aria-label="Remove Vendor From Favorites">Remove</a>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>No saved vendors yet.</p>
      <?php endif; ?>
    </div>
  </div>
</main>

<?php require_once 'private/footer.php'; ?>
