<?php
$page_title = "Vendors Attending";
require_once 'private/initialize.php';
require_once 'private/header.php';

// Get week_id from URL
$week_id = $_GET['week_id'] ?? 0;

// Fetch market details
$sql = "SELECT week_start, week_end FROM market_week WHERE week_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$week_id]);
$market = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch vendors attending this market
$sql = "SELECT v.business_name, v.vendor_bio, v.city, s.state_abbr 
        FROM vendor_market vm
        JOIN vendor v ON vm.vendor_id = v.vendor_id
        JOIN state s ON v.state_id = s.state_id
        WHERE vm.week_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$week_id]);
$vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Vendors Attending (<?= htmlspecialchars($market['week_start']) ?> - <?= htmlspecialchars($market['week_end']) ?>)</h2>
<ul>
  <?php foreach ($vendors as $vendor): ?>
    <li>
      <strong><?= htmlspecialchars($vendor['business_name']) ?></strong> -
      <?= htmlspecialchars($vendor['city'] . ", " . $vendor['state_abbr']) ?>
      <p><?= nl2br(htmlspecialchars($vendor['vendor_bio'])) ?></p>
    </li>
  <?php endforeach; ?>
</ul>

<?php require_once 'private/footer.php'; ?>
