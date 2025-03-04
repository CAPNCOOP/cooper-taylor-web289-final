<?php
$page_title = "Vendor - Profile";
require_once 'private/initialize.php';
require_once 'private/header.php';

// Get vendor ID from URL
$vendor_id = $_GET['vendor_id'] ?? 0;

// Fetch vendor details & products
$sql = "SELECT v.vendor_id, v.business_name, v.vendor_bio, v.business_email, v.website, v.city, s.state_abbr,
               pi.file_path AS profile_image, 
               p.product_id, p.name AS product_name, p.price, p.description, pimg.file_path AS product_image
        FROM vendor v
        LEFT JOIN profile_image pi ON v.user_id = pi.user_id
        LEFT JOIN product p ON v.vendor_id = p.vendor_id
        LEFT JOIN product_image pimg ON p.product_id = pimg.product_id
        LEFT JOIN state s ON v.state_id = s.state_id
        WHERE v.vendor_id = ?";


$stmt = $db->prepare($sql);
$stmt->execute([$vendor_id]);
$vendor_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch vendor's upcoming markets
$sql = "SELECT mw.week_start, mw.week_end, s.state_abbr
        FROM vendor_market vm
        LEFT JOIN market_week mw ON vm.week_id = mw.week_id
        LEFT JOIN state s ON mw.market_id = s.state_id
        WHERE vm.vendor_id = ? AND mw.week_start >= CURDATE()
        ORDER BY mw.week_start ASC";
$stmt = $db->prepare($sql);
$stmt->execute([$vendor_id]);
$markets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If no vendor found, redirect
if (!$vendor_data) {
  header("Location: ourvendors.php");
  exit;
}

// Extract vendor details
$vendor = $vendor_data[0];

?>

<div id="vendor-profile-container">
  <div id="vendor-profile-card">
    <h2><?php echo htmlspecialchars($vendor['business_name']); ?></h2>
    <img src="img/upload/users/<?php echo htmlspecialchars($vendor['profile_image'] ?? 'default.png'); ?>" height="250" width="250" alt="A Vendor Image.">
    <p><strong>Bio:</strong> <?php echo nl2br(htmlspecialchars($vendor['vendor_bio'])); ?></p>
    <p><strong>Contact:</strong> <?php echo htmlspecialchars($vendor['business_email']); ?></p>
    <?php if ($vendor['website']): ?>
      <p><strong>Website:</strong> <a href="<?php echo htmlspecialchars($vendor['website']); ?>" target="_blank">Visit Website</a></p>
    <?php endif; ?>
    <p><strong>Location:</strong> <?php echo htmlspecialchars($vendor['city'] . ', ' . $vendor['state_abbr']); ?></p>

    <!-- JavaScript-powered button -->
    <button class="favorite-btn" data-vendor-id="<?= $vendor['vendor_id'] ?>">♡</button>

    <!-- PHP Fallback for Non-JS Browsers -->
    <noscript>
      <form action="favorite_vendor.php" method="POST">
        <input type="hidden" name="vendor_id" value="<?= $vendor['vendor_id'] ?>">
        <button type="submit">♡</button>
      </form>
    </noscript>

    <!-- Notification Element -->
    <div id="notification" class="hidden"></div>

    <?php if (isset($_GET['message'])): ?>
      <div class="notification">
        <?= htmlspecialchars($_GET['message']) ?>
      </div>
    <?php endif; ?>
  </div>

  <div id="vendor-upcoming-markets">
    <h2>Upcoming Markets</h2>
    <ul>
      <?php foreach ($markets as $market): ?>
        <li>
          <strong>Market Week:</strong>
          <?php echo htmlspecialchars($market['week_start'] . " - " . $market['week_end'] . " | " . $market['state_abbr']); ?>
        </li>
      <?php endforeach; ?>

      <?php if (empty($markets)): ?>
        <li>No upcoming markets scheduled.</li>
      <?php endif; ?>
    </ul>
  </div>
</div>

<h2>Products</h2>
<div class="product-list">
  <?php foreach ($vendor_data as $product): ?>
    <?php if ($product['product_name']): ?>
      <div class="product-card">
        <img src="img/upload/products/<?php echo htmlspecialchars($product['product_image'] ?? 'default_product.jpg'); ?>" height="250" width="250" alt="Product Image">
        <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
        <p>Price: $<?php echo number_format($product['price'], 2); ?></p>
        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
      </div>
    <?php endif; ?>
  <?php endforeach; ?>
</div>

<?php require_once 'private/footer.php'; ?>
