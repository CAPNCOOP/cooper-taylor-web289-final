<?php
$page_title = "View Product";
require_once 'private/initialize.php';
require_once 'private/header.php';

$product_id = $_GET['product_id'] ?? null;

if (!$product_id || !is_numeric($product_id)) {
  $session->message("❌ Invalid product.");
  redirect_to('index.php');
}

$product = Product::findById($product_id);
if (!$product) {
  $session->message("❌ Product not found.");
  redirect_to('index.php');
}

$vendor = Vendor::find_by_id($product['vendor_id']);
$profile_image = !empty($product['product_image'])
  ? $product['product_image']
  : 'products/default_product.webp';
?>

<h2><?= h($product['name']) ?></h2>
<div class="product-full-view">
  <a href="vendor_profile.php?vendor_id=<?= h($product['vendor_id']) ?>" class="back-link" aria-label="Back to Vendor">
    ← Back to Vendor
  </a>

  <img src="img/upload/<?= h($profile_image) ?>" alt="<?= h($product['name']) ?>" width="500" height="500" loading="lazy">

  <p><strong>Price:</strong> $<?= number_format($product['price'], 2) ?></p>
  <p><strong>Per:</strong> <?= h($product['amount_name'] ?? 'unit') ?></p>
  <p><strong>Category:</strong> <?= h($product['category_name'] ?? 'Uncategorized') ?></p>
  <p><strong>Description:</strong> <?= nl2br(h($product['description'])) ?></p>

  <?php
  $tags = Product::fetchTags($product_id);
  if (!empty($tags)): ?>
    <p><strong>Tags:</strong>
      <?php foreach ($tags as $tag): ?>
        <?php if (is_string($tag)): ?>
          <span class="tag"><?= h($tag) ?></span>
        <?php endif; ?>
      <?php endforeach; ?>
    </p>
  <?php endif; ?>

  <?php if ($vendor): ?>
    <p><strong>Sold by:</strong>
      <a href="vendor_profile.php?vendor_id=<?= h($vendor->vendor_id) ?>">
        <?= h($vendor->business_name) ?>
      </a>
    </p>
  <?php endif; ?>
</div>

<?php require_once 'private/footer.php'; ?>
