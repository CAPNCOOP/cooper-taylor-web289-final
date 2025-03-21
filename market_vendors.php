<?php
$page_title = "Vendors Attending";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_once 'private/functions.php';

// Get week_id from URL
$week_id = $_GET['week_id'] ?? 0;

// Fetch market details
$sql = "SELECT week_start, week_end FROM market_week WHERE week_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$week_id]);
$market = $stmt->fetch(PDO::FETCH_ASSOC);

// Format the week_end date
$week_end_formatted = strtoupper(date('M-d-Y', strtotime($market['week_end'])));

// Fetch vendors attending this market with correctly mapped profile images
$sql = "SELECT v.vendor_id, v.user_id, v.business_name, v.vendor_bio, v.city, s.state_abbr, 
               pi.file_path AS profile_photo
        FROM vendor_market vm
        JOIN vendor v ON vm.vendor_id = v.vendor_id
        JOIN state s ON v.state_id = s.state_id
        LEFT JOIN profile_image pi ON v.user_id = pi.user_id  -- Correct mapping
        WHERE vm.week_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$week_id]);
$vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Vendors Attending (<?= h($week_end_formatted) ?>)</h2>
<ul class="week-vendor">
  <?php foreach ($vendors as $vendor): ?>
    <?php
    // Fix file path issue: Ensure it's not NULL and format correctly
    $profile_photo = !empty($vendor['profile_photo']) ? '/' . ltrim($vendor['profile_photo'], '/') : 'img/upload/users/default-profile.png';
    ?>
    <a href="vendor_profile.php?vendor_id=<?= h($vendor['vendor_id']) ?>" style="text-decoration: none; color: inherit;">
      <li class="vendor-item">
        <!-- Display Profile Photo -->
        <img src="<?= h($profile_photo) ?>"
          alt="<?= h($vendor['business_name']) ?>"
          class="vendor-photo"
          onerror="this.onerror=null;this.src='img/upload/users/default.png';" height="100" width="100">
        <div class="vendor-info">
          <strong><?= h($vendor['business_name']) ?></strong> -
          <?= h($vendor['city'] . ", " . $vendor['state_abbr']) ?>
          <p><?= nl2br(h($vendor['vendor_bio'])) ?></p>
        </div>
      </li>
    </a>
  <?php endforeach; ?>
</ul>

<?php require_once 'private/footer.php'; ?>
