<?php
$page_title = "Vendor Dashboard";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_once 'private/functions.php';

require_login();

if (!isset($db)) {
  exit("Database connection error.");
}

$user_id = $_SESSION['user_id'] ?? null;
$user_level = $_SESSION['user_level_id'] ?? null;

if (!$user_id || $user_level !== 2) {
  redirect_to('login.php');
}

// ✅ Load vendor and profile info using OOP
$vendor = Vendor::find_by_user_id($user_id);
$user = User::find_by_id($user_id);

if (!$vendor || !$user) {
  exit("Error: Vendor not found.");
}

$profile_image = get_profile_image($user_id);
$vendor_status = $vendor->vendor_status ?? 'pending';

require_once 'private/popup_message.php';
?>

<?php if ($vendor_status === 'pending'): ?>
  <div class="popup frozen-popup">
    <p>🚫 To post products, you must be approved. Your account is currently pending. Please allow staff up to 48 hours to review your information. Check back later.</p>
  </div>
<?php elseif ($vendor_status === 'denied'): ?>
  <div class="popup frozen-popup">
    <p>❌ Your vendor application was denied. Please contact support for more information.</p>
  </div>
<?php endif; ?>


<div id="vendor-info">
  <div>
    <h2><?= h($user->first_name . ' ' . $user->last_name); ?></h2>
    <img src="<?= h($profile_image); ?>" alt="Vendor Profile Picture" height="250" width="250">
    <span>Business: <?= h($vendor->business_name); ?></span>
    <a href="<?= ($vendor_status === 'approved') ? 'edit_profile.php' : '#' ?>"
      class="btn <?= ($vendor_status === 'approved') ? '' : 'disabled-link' ?>"
      title="<?= ($vendor_status === 'approved') ? '' : 'Approval required to edit profile.' ?>">
      <img src="img/assets/edit.png" alt="An edit icon." height="40" width="40">
      Edit Details
    </a>
  </div>

  <div>
    <h2>Dashboard Overview</h2>
    <p>Manage your products, RSVP for upcoming markets, and update your business profile.</p>
    <nav>
      <ul>
        <li>
          <a href="<?= ($vendor_status === 'approved') ? 'manage_products.php' : '#' ?>"
            class="<?= ($vendor_status === 'approved') ? '' : 'disabled-link' ?>"
            title="<?= ($vendor_status === 'approved') ? '' : 'Approval required to manage products.' ?>">
            Manage Products
          </a>
        </li>

        <li>
          <a href="<?= ($vendor_status === 'approved') ? 'rsvp_market.php' : '#' ?>"
            class="<?= ($vendor_status === 'approved') ? '' : 'disabled-link' ?>"
            title="<?= ($vendor_status === 'approved') ? '' : 'Approval required to RSVP for markets.' ?>">
            RSVP for Markets
          </a>
        </li>
      </ul>
    </nav>
  </div>
</div>

<?php require_once 'private/footer.php'; ?>
