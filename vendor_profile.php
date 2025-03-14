<?php
$page_title = "Vendor Profile";
require_once 'private/initialize.php';
require_once 'private/header.php';

// ✅ Validate vendor_id
$vendor_id = isset($_GET['vendor_id']) ? filter_var($_GET['vendor_id'], FILTER_VALIDATE_INT) : 0;
if (!$vendor_id) {
  $_SESSION['message'] = "❌ Invalid vendor selected.";
  header("Location: ourvendors.php");
  exit();
}

// ✅ Fetch Vendor Details
$sql = "SELECT v.vendor_id, v.business_name, v.vendor_bio, v.description, v.business_email, v.website, v.city, s.state_abbr,
               u.first_name, u.last_name, pi.file_path AS profile_image
        FROM vendor v
        LEFT JOIN users u ON v.user_id = u.user_id
        LEFT JOIN profile_image pi ON u.user_id = pi.user_id
        LEFT JOIN state s ON v.state_id = s.state_id
        WHERE v.vendor_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$vendor_id]);
$vendor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vendor) {
  $_SESSION['message'] = "❌ Vendor not found.";
  header("Location: ourvendors.php");
  exit();
}

// ✅ Fetch Products Separately
$sql = "SELECT p.product_id, p.name AS product_name, p.price, p.description AS product_description, 
               a.amount_name, pimg.file_path AS product_image
        FROM product p
        LEFT JOIN product_image pimg ON p.product_id = pimg.product_id
        LEFT JOIN amount_offered a ON p.amount_id = a.amount_id  
        WHERE p.vendor_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$vendor_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Fetch Upcoming Markets
$sql = "SELECT mw.week_start, mw.week_end, s.state_abbr
        FROM vendor_market vm
        LEFT JOIN market_week mw ON vm.week_id = mw.week_id
        LEFT JOIN state s ON mw.market_id = s.state_id
        WHERE vm.vendor_id = ? AND mw.week_start >= CURDATE()
        ORDER BY mw.week_start ASC";
$stmt = $db->prepare($sql);
$stmt->execute([$vendor_id]);
$markets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Check if Vendor is Favorited
$is_favorited = false;
if (isset($_SESSION['user_id']) && $_SESSION['user_level_id'] == 1) {
  $sql = "SELECT 1 FROM favorite WHERE user_id = ? AND vendor_id = ?";
  $stmt = $db->prepare($sql);
  $stmt->execute([$_SESSION['user_id'], $vendor_id]);
  $is_favorited = (bool) $stmt->fetchColumn();
}

require_once 'private/popup_message.php';
?>

<h2><?= htmlspecialchars($vendor['business_name']); ?></h2>
<div id="vendor-profile-container">
  <div id="vendor-profile-card">
    <img src="<?= htmlspecialchars($vendor['profile_image'] ?? 'default.png'); ?>" height="150" width="150" alt="Vendor Image">
    <p><strong>Bio:</strong> <?= nl2br(htmlspecialchars($vendor['vendor_bio'])); ?></p>
    <p><strong>Contact:</strong> <?= htmlspecialchars($vendor['business_email']); ?></p>

    <?php if ($vendor['website']): ?>
      <p><strong>Website:</strong> <a href="<?= htmlspecialchars($vendor['website']); ?>" target="_blank">Visit Website</a></p>
    <?php endif; ?>

    <p><strong>Location:</strong> <?= htmlspecialchars($vendor['city'] . ', ' . $vendor['state_abbr']); ?></p>

    <!-- Favorite Button -->
    <?php if (isset($_SESSION['user_level_id']) && $_SESSION['user_level_id'] == 1): ?>
      <button class="favorite-btn" data-vendor-id="<?= $vendor['vendor_id'] ?>">
        <?= $is_favorited ? '❤️' : '♡' ?>
      </button>
    <?php endif; ?>
  </div>
</div>

<!-- Product List -->
<h2>Products</h2>
<div class="product-list">
  <?php foreach ($products as $product): ?>
    <div class="product-card">
      <img src="img/upload/products/<?= htmlspecialchars($product['product_image'] ?? 'default_product.png'); ?>" height="250" width="250" alt="Product Image">
      <h3><?= htmlspecialchars($product['product_name']); ?></h3>
      <p><strong>Price:</strong> $<?= number_format($product['price'], 2); ?></p>
      <p><strong>Per:</strong> <?= htmlspecialchars($product['amount_name'] ?? 'unit'); ?></p>
      <p><?= nl2br(htmlspecialchars($product['product_description'])); ?></p>
    </div>
  <?php endforeach; ?>
</div>

<?php require_once 'private/footer.php'; ?>
