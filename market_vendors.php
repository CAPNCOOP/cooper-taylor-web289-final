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
$vendors = $admin->fetchVendorsForMarketWeek($week_id);

$week_end_formatted = strtoupper(date('M-d-Y', strtotime($market['week_end'] ?? '')));
?>

<h2>Vendors Attending (<?= h($week_end_formatted) ?>)</h2>

<ul class="week-vendor">
  <?php foreach ($vendors as $vendor): ?>
    <?php
    $profile_photo = !empty($vendor['profile_photo'])
      ? '/' . ltrim($vendor['profile_photo'], '/')
      : 'img/upload/users/default-profile.png';
    ?>
    <a href="vendor_profile.php?vendor_id=<?= h($vendor['vendor_id']) ?>" style="text-decoration: none; color: inherit;">
      <li class="vendor-item">
        <img src="<?= h($profile_photo) ?>"
          alt="<?= h($vendor['business_name']) ?>"
          class="vendor-photo"
          onerror="this.onerror=null;this.src='img/upload/users/default.png';"
          height="100" width="100">
        <div>
          <strong><?= h($vendor['business_name']) ?></strong> -
          <?= h($vendor['city'] . ", " . $vendor['state_abbr']) ?>
          <p><?= nl2br(h($vendor['vendor_bio'])) ?></p>
        </div>
      </li>
    </a>
  <?php endforeach; ?>
</ul>

<?php require_once 'private/footer.php'; ?>
