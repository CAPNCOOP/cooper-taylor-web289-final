<?php
$page_title = "Vendors Attending";
require_once 'private/initialize.php';
require_once 'private/header.php';

// Validate week_id
$week_id = $_GET['week_id'] ?? 0;
if (!$week_id || !is_numeric($week_id)) {
  $session->message("❌ Invalid market week.");
  redirect_to('index.php');
}

$admin = new Admin();
$market = $admin->fetchMarketById($week_id);
$vendors_data = $admin->fetchVendorsForMarketWeek($week_id);

$vendors = array_map(fn($data) => (object) $data, $vendors_data);

$week_end_formatted = strtoupper(date('M-d-Y', strtotime($market['week_end'] ?? '')));
?>

<h2>Vendors Attending (<?= h($week_end_formatted) ?>)</h2>

<ul class="week-vendor">
  <?php foreach ($vendors as $vendor): ?>
    <?php
    $profile_path = $vendor->profile_photo && file_exists(__DIR__ . '/../img/upload/' . $vendor->profile_photo)
      ? 'img/upload/' . ltrim($vendor->profile_photo, '/')
      : 'img/upload/users/default.webp';
    ?>
    <li class="vendor-item">
      <a href="vendor_profile.php?vendor_id=<?= h($vendor->vendor_id) ?>">
        <img
          src="<?= h($profile_path) ?>"
          alt="Photo of <?= h($vendor->business_name) ?>"
          class="vendor-photo"
          height="100"
          width="100"
          loading="lazy">
        <div>
          <strong><?= h($vendor->business_name) ?></strong> – <?= h($vendor->city . ", " . $vendor->state_abbr) ?>
          <p><?= nl2br(h($vendor->vendor_bio)) ?></p>
        </div>
      </a>
    </li>
  <?php endforeach; ?>
</ul>

<?php require_once 'private/footer.php'; ?>
