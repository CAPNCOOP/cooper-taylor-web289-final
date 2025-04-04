<?php
$page_title = "Vendor Profile";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_once 'private/functions.php';

// Get vendor ID from URL
$vendor_id = $_GET['vendor_id'] ?? 0;

// Fetch vendor details & products
$sql = "SELECT v.vendor_id, v.business_name, v.vendor_bio, v.description, v.business_email, v.website, v.city, s.state_abbr,
               u.first_name, u.last_name, 
               pi.file_path AS profile_image, 
               p.product_id, p.name AS product_name, p.price, p.description AS product_description, 
               a.amount_name, 
               pimg.file_path AS product_image
        FROM vendor v
        LEFT JOIN users u ON v.user_id = u.user_id
        LEFT JOIN profile_image pi ON v.user_id = pi.user_id
        LEFT JOIN product p ON v.vendor_id = p.vendor_id
        LEFT JOIN product_image pimg ON p.product_id = pimg.product_id
        LEFT JOIN amount_offered a ON p.amount_id = a.amount_id  
        LEFT JOIN state s ON v.state_id = s.state_id
        WHERE v.vendor_id = ?";

$stmt = $db->prepare($sql);
$stmt->execute([$vendor_id]);
$vendor_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch vendor's upcoming markets with market_status from market_week
$sql = "SELECT mw.week_start, mw.week_end, mw.market_status
        FROM vendor_market vm
        LEFT JOIN market_week mw ON vm.week_id = mw.week_id
        WHERE vm.vendor_id = ? AND mw.week_start >= CURDATE()
        ORDER BY mw.week_start ASC
        LIMIT 5";
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
<?php require_once 'private/popup_message.php'; ?>

<h2><?php echo h($vendor['business_name']); ?></h2>
<div id="vendor-profile-container">
  <div id="vendor-profile-card">
    <img src="<?php echo h($vendor['profile_image'] ?? 'default.png'); ?>" height="250" width="250" alt="A Vendor Image.">
    <div>
      <p>"<?php echo nl2br(h($vendor['vendor_bio'])); ?>"</p>

      <?php if (isset($_SESSION['user_level_id']) && $_SESSION['user_level_id'] == 1): ?>
        <button class="favorite-btn" data-vendor-id="<?= $vendor['vendor_id'] ?>">♡</button>
        <noscript>
          <form action="favorite_vendor.php" method="POST">
            <input type="hidden" name="vendor_id" value="<?= $vendor['vendor_id'] ?>">
            <button type="submit">♡</button>
          </form>
        </noscript>
      <?php endif; ?>

      <div id="notification" class="hidden"></div>

      <?php if (isset($_GET['message'])): ?>
        <div class="notification">
          <?= h($_GET['message']) ?>
        </div>
      <?php endif; ?>
    </div>
  </div>


  <div id="vendor-upcoming-markets">
    <h2>Upcoming Markets</h2>
    <ul>
      <?php foreach ($markets as $market): ?>
        <li>
          <strong>Market Date:</strong>
          <?php
          $formatted_date = strtoupper(date('M-d-Y', strtotime($market['week_end'])));
          echo h($formatted_date . " - " . ucfirst($market['market_status']));
          ?>
        </li>
      <?php endforeach; ?>
      <?php if (empty($markets)): ?>
        <li>No upcoming markets scheduled.</li>
      <?php endif; ?>
    </ul>
  </div>

</div>

<div id="vendor-contact-info">
  <h2>Contact & Info</h2>
  <div>
    <p><strong>Email:</strong> <?php echo h($vendor['business_email']); ?></p>
    <?php if ($vendor['website']): ?>

      <p><strong>Website:</strong> <a href="https://www.linkedin.com/in/tcooper1412/" target="_blank"><?php echo h($vendor['business_name']); ?></a></p>
    <?php endif; ?>

    <p><strong>Location:</strong> <?php echo h($vendor['city']); ?></p>
  </div>
</div>

<h2>Products</h2>
<div class="product-list">
  <?php foreach ($vendor_data as $product): ?>
    <?php if (!empty($product['product_name'])): ?>
      <div class="product-card">
        <?php
        // Ensure a fallback image if no product image exists
        $product_image = !empty($product['product_image']) ? htmlspecialchars($product['product_image']) : 'default_product.png';
        ?>
        <img src="img/upload/products/<?= $product_image; ?>" height="250" width="250" alt="Product Image">
        <h3><?= htmlspecialchars($product['product_name']); ?></h3>
        <p><strong>Price:</strong> $<?= number_format($product['price'], 2); ?></p> <!-- Price on its own line -->
        <p><strong>Per:</strong> <?= htmlspecialchars($product['amount_name'] ?? 'unit'); ?></p> <!-- Amount on separate line -->
        <p><?= nl2br(htmlspecialchars($product['product_description'])); ?></p> <!-- Product description -->
      </div>
    <?php endif; ?>
  <?php endforeach; ?>
</div>

<?php require_once 'private/footer.php'; ?>
