<?php
$page_title = "Vendor Dashboard";
require_once 'private/initialize.php';
require_once 'private/header.php';

// ✅ Ensure user is logged in and is a Vendor
if (!isset($_SESSION['user_id']) || $_SESSION['user_level_id'] != 2) {
  $_SESSION['message'] = "❌ Unauthorized access.";
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch Vendor and User Details
$sql = "SELECT v.vendor_id, v.business_name, v.vendor_status, u.first_name, u.last_name, p.file_path AS profile_image
        FROM vendor v
        JOIN users u ON v.user_id = u.user_id
        LEFT JOIN profile_image p ON u.user_id = p.user_id
        WHERE v.user_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id]);
$vendor = $stmt->fetch(PDO::FETCH_ASSOC);

// ✅ Ensure vendor exists, otherwise redirect
if (!$vendor) {
  $_SESSION['message'] = "❌ Vendor profile not found.";
  header("Location: dashboard.php");
  exit();
}

// ✅ Assign vendor status properly
$vendor_status = $vendor['vendor_status'] ?? 'pending';
$is_approved = ($vendor_status === 'approved');

// ✅ Set profile image (default if not found)
$profile_image = !empty($vendor['profile_image']) ? $vendor['profile_image'] : "img/upload/users/default.png";

// ✅ Centralized Messages
require_once 'private/popup_message.php';
?>

<div id="vendor-info">
  <div>
    <h2><?= htmlspecialchars($vendor['first_name'] . ' ' . $vendor['last_name']); ?></h2>
    <img src="<?= htmlspecialchars($profile_image); ?>" alt="Vendor Profile Picture" height="250" width="250">
    <span>Business: <?= htmlspecialchars($vendor['business_name']); ?></span>

    <a href="<?= $is_approved ? 'edit_profile.php' : '#' ?>"
      class="btn <?= $is_approved ? '' : 'disabled-link' ?>"
      title="<?= $is_approved ? '' : 'Approval required to edit profile.' ?>">
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
          <a href="<?= $is_approved ? 'manage_products.php' : '#' ?>"
            class="<?= $is_approved ? '' : 'disabled-link' ?>"
            title="<?= $is_approved ? '' : 'Approval required to manage products.' ?>">
            Manage Products
          </a>
        </li>

        <li>
          <a href="<?= $is_approved ? 'rsvp_market.php' : '#' ?>"
            class="<?= $is_approved ? '' : 'disabled-link' ?>"
            title="<?= $is_approved ? '' : 'Approval required to RSVP for markets.' ?>">
            RSVP for Markets
          </a>
        </li>
      </ul>
    </nav>
  </div>
</div>

<?php require_once 'private/footer.php'; ?>
