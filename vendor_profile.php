<?php
$page_title = "Vendor Profile";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_once 'private/functions.php';

// Get vendor ID from URL
$vendor_id = $_GET['vendor_id'] ?? 0;

// Use OOP to fetch vendor
$vendor = Vendor::find_by_id($vendor_id);
if (!$vendor) {
  redirect_to('ourvendors.php');
}

// Fetch profile image
$profile_image = get_profile_image($vendor->user_id);

// Fetch vendor's upcoming markets
$markets = Vendor::fetchUpcomingMarkets($vendor_id);

// Fetch products (with images & amount names)
$products = Vendor::fetchProducts($vendor_id);

?>

<?php require_once 'private/popup_message.php'; ?>

<h2><?= h($vendor->business_name) ?></h2>
<div id="vendor-profile-container">
  <div id="vendor-profile-card">
    <img src="<?= h($profile_image) ?>" height="250" width="250" alt="A Vendor Image.">
    <div>
      <p>"<?= nl2br(h($vendor->vendor_bio)) ?>"</p>

      <?php if (isset($_SESSION['user_level_id']) && $_SESSION['user_level_id'] == 1): ?>
        <button class="favorite-btn" data-vendor-id="<?= h($vendor->vendor_id) ?>">♡</button>
        <noscript>
          <form action="favorite_vendor.php" method="POST">
            <input type="hidden" name="vendor_id" value="<?= h($vendor->vendor_id) ?>">
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
          echo h("$formatted_date - " . ucfirst($market['market_status']));
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
    <p><strong>Email:</strong> <?= h($vendor->business_email) ?></p>
    <?php if ($vendor->website): ?>
      <p><strong>Website:</strong> <a href="https://www.linkedin.com/in/tcooper1412/" target="_blank"><?= h($vendor->business_name) ?></a></p>
    <?php endif; ?>
    <p><strong>Location:</strong> <?= h($vendor->city) ?></p>
  </div>
</div>

<h2>Products</h2>
<div class="product-list">
  <?php foreach ($products as $product): ?>
    <?php if (!empty($product['name'])): ?>
      <div class="product-card">
        <?php $product_image = !empty($product['product_image']) ? $product['product_image'] : 'default_product.png'; ?>
        <img src="img/upload/products/<?= h($product_image) ?>" height="250" width="250" alt="Product Image">
        <h3><?= h($product['name']) ?></h3>
        <p><strong>Price:</strong> $<?= number_format($product['price'], 2) ?></p>
        <p><strong>Per:</strong> <?= h($product['amount_name'] ?? 'unit') ?></p>
        <p><?= nl2br(h($product['description'])) ?></p>
      </div>
    <?php endif; ?>
  <?php endforeach; ?>
</div>

<?php require_once 'private/footer.php'; ?>
