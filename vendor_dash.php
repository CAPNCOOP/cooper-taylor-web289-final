<?php
$page_title = "Vendor Dashboard";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_once 'private/functions.php';

if (!isset($db)) {
  exit("Database connection error.");
}

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit("Redirecting to login...");
}

// Fetch user and vendor details
$user_id = $_SESSION['user_id'];

$sql = "SELECT v.vendor_id, v.business_name, v.vendor_status, u.first_name, u.last_name, p.file_path AS profile_image
        FROM vendor v
        JOIN users u ON v.user_id = u.user_id
        LEFT JOIN profile_image p ON u.user_id = p.user_id
        WHERE v.user_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id]);
$vendor = $stmt->fetch(PDO::FETCH_ASSOC);

// Ensure vendor exists
if (!$vendor) {
  exit("Error: Vendor not found.");
}

// ✅ Assign vendor status properly
$vendor_status = $vendor['vendor_status'] ?? 'pending';

// Set profile image (default if not found)
$profile_image = !empty($vendor['profile_image']) ? $vendor['profile_image'] : "img/upload/users/default.png";

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
    <h2><?php echo h($vendor['first_name'] . ' ' . $vendor['last_name']); ?></h2>
    <img src="<?php echo h($profile_image); ?>" alt="Vendor Profile Picture" height="250" width="250">
    <span>Business: <?php echo h($vendor['business_name']); ?></span>
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
