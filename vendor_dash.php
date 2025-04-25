<?php
$page_title = "Vendor Dashboard";
require_once 'private/initialize.php';
require_once 'private/header.php';
Session::require_login();
Session::require_vendor();

// Redirect non-vendors
if (!Session::is_vendor()) {
  redirect_to('login.php');
}

$user_id = Session::user_id();
$user = User::find_by_id($user_id);
$vendor = Vendor::find_by_user_id($user_id);

if (!$user || !$vendor) {
  exit("Error: Vendor not found.");
}

$profile_image = get_profile_image($user_id);
$vendor_status = $vendor->vendor_status;

require_once 'private/popup_message.php';
?>

<?php if ($vendor->isPending()): ?>
  <div class="popup frozen-popup">
    <p>🚫 To post products, you must be approved. Your account is currently pending. Please allow staff up to 48 hours to review your information. Check back later.</p>
  </div>
<?php elseif ($vendor->isDenied()): ?>
  <div class="popup frozen-popup">
    <p>❌ Your vendor application was denied. Please contact support for more information.</p>
  </div>
<?php endif; ?>

<div id="vendor-info">
  <div>
    <h2><?= h($user->first_name . ' ' . $user->last_name); ?></h2>
    <img src="<?= h('img/upload/' . $user->getImagePath()) ?>" alt="Vendor Profile Picture" height="250" width="250" loading="lazy">
    <span>Business: <?= h($vendor->business_name); ?></span>
    <a href="<?= $vendor->isApproved() ? 'edit_profile.php' : '#' ?>"
      class="btn <?= $vendor->isApproved() ? '' : 'disabled-link' ?>"
      title="<?= $vendor->isApproved() ? '' : 'Approval required to edit profile.' ?>" aria-label="Edit Profile Details">
      <img src="img/assets/edit.png" alt="An edit icon." height="40" width="40" loading="lazy">
      Edit Details
    </a>
  </div>

  <div>
    <h2>Dashboard Overview</h2>
    <p>Manage your products, RSVP for upcoming markets, and update your business profile.</p>
    <nav>
      <ul>
        <li>
          <a href="<?= $vendor->isApproved() ? 'manage_products.php' : '#' ?>"
            class="<?= $vendor->isApproved() ? '' : 'disabled-link' ?>"
            title="<?= $vendor->isApproved() ? '' : 'Approval required to manage products.' ?>" aria-label="Manage Products">
            Manage Products
          </a>
        </li>

        <li>
          <a href="<?= $vendor->isApproved() ? 'rsvp_market.php' : '#' ?>"
            class="<?= $vendor->isApproved() ? '' : 'disabled-link' ?>"
            title="<?= $vendor->isApproved() ? '' : 'Approval required to RSVP for markets.' ?>" aria-label="RSVP for Markets">
            RSVP for Markets
          </a>
        </li>
      </ul>
    </nav>
  </div>
</div>

<?php require_once 'private/footer.php'; ?>
