<?php
$page_title = "Vendor Profile";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_once 'private/functions.php';

// Get vendor ID from URL
$vendor_id = $_GET['vendor_id'] ?? 0;

// Get Search Term
$searchTerm = $_GET['search'] ?? '';

$itemsPerPage = 12;
$page = (int)($_GET['page'] ?? 1);
$offset = ($page - 1) * $itemsPerPage;

$products = Vendor::fetchPaginatedProducts($vendor_id, $itemsPerPage, $offset, $searchTerm);
$totalProducts = Vendor::countVendorProducts($vendor_id, $searchTerm);
$totalPages = ceil($totalProducts / $itemsPerPage);

$paginationBaseUrl = "vendor_profile.php?vendor_id=" . urlencode($vendor_id) . "&search=" . u($searchTerm);

// Use OOP to fetch vendor
$vendor = Vendor::find_by_id($vendor_id);
if (!$vendor) {
  redirect_to('ourvendors.php');
}

// Fetch profile image
$profile_image = get_profile_image($vendor->user_id);

// Fetch vendor's upcoming markets
$markets = Vendor::fetchUpcomingMarkets($vendor_id);

?>

<?php require_once 'private/popup_message.php'; ?>

<h2><?= h($vendor->business_name) ?></h2>
<div id="vendor-profile-container">
  <div id="vendor-profile-card">
    <img src="<?= h('img/upload/' . $profile_image) ?>" height="250" width="250" alt="A Vendor Image." loading="lazy">
    <div>
      <p>"<?= nl2br(h($vendor->vendor_bio)) ?>"</p>

      <?php if (isset($_SESSION['user_level_id']) && $_SESSION['user_level_id'] == 1): ?>
        <button class="favorite-btn" data-vendor-id="<?= h($vendor->vendor_id) ?>">♡</button>
        <noscript>
          <form action="favorite_vendor.php" method="POST" role="form">
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

<form method="GET" action="vendor_profile.php" role="search" id="vendor-search-form" role="form">
  <input type="hidden" name="vendor_id" value="<?= h($searchTerm) ?>">
  <input
    type="text"
    id="search"
    name="search"
    placeholder="Search this vendor's products..."
    value="<?= h($searchTerm) ?>"
    aria-label="Search Products">
  <button type="submit" aria-label="Search Products">Search</button>
</form>

<div class="profile-product-list">
  <?php if (empty($products)): ?>
    <p>No products found<?= $searchTerm ? ' for "' . h($searchTerm) . '"' : '' ?>.</p>
  <?php endif; ?>
  <?php foreach ($products as $product): ?>
    <?php if (!empty($product['name'])): ?>
      <div class="profile-product-card" data-product='<?= htmlspecialchars(json_encode([
                                                        'id' => $product['product_id'],
                                                        'name' => $product['name'],
                                                        'price' => number_format($product['price'], 2),
                                                        'amount' => $product['amount_name'] ?? 'unit',
                                                        'description' => $product['description'],
                                                        'image' => $product['product_image'] ?? 'products/default_product.webp',
                                                        'category' => $product['category_name'] ?? 'Uncategorized',
                                                        'tags' => $product['tags'] ?? []
                                                      ]), ENT_QUOTES, 'UTF-8') ?>'>
        <?php
        $product_image = !empty($product['product_image'])
          ? h($product['product_image'])
          : 'default_product.webp';
        ?>

        <img src="/img/upload/<?= $product_image ?>"
          height="300"
          width="300"
          alt="Product Image"
          loading="lazy" />

        <h3><?= h($product['name']) ?></h3>
        <div class="card-footer">
          <a href="show_product.php?product_id=<?= h($product['product_id']) ?>" class="card-footer-link" aria-label="View Full Product Page">View Full Product</a>
        </div>
      </div>
    <?php endif; ?>
  <?php endforeach; ?>
</div>

<!-- Product Modal -->
<div id="product-modal" class="modal-overlay" style="display:none;">
  <div class="modal-content">
    <button class="modal-close" aria-label="Close">&times;</button>
    <img id="modal-product-image" src="" alt="Product Image" height="300" width="300" loading="lazy" />
    <h2 id="modal-product-name"></h2>
    <p><strong>Price:</strong> $<span id="modal-product-price"></span></p>
    <p><strong>Per:</strong> <span id="modal-product-amount"></span></p>
    <p id="modal-product-description"></p>
    <p><strong>Category:</strong> <span id="modal-product-category"></span></p>
    <p id="modal-product-tags-wrapper" style="display: none;">
      <strong>Tags:</strong>
      <span id="modal-product-tags"></span>
    </p>
  </div>
</div>

<?php require_once 'private/pagination.php'; ?>

<?php require_once 'private/footer.php'; ?>
