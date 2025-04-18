<?php
require_once 'private/initialize.php';
require_once 'private/header.php';

$product_id = $_GET['id'] ?? 0;

if (!$product_id || !is_numeric($product_id)) {
  $session->message("❌ Invalid product.");
  redirect_to('index.php');
}

$product = Product::findById($product_id);
if (!$product) {
  $session->message("❌ Product not found.");
  redirect_to('index.php');
}

$vendor = Vendor::find_by_id($product->vendor_id);
$profile_image = $product->product_image ?: 'products/default_product.webp';
?>

<h2><?= h($product->name) ?></h2>
<div class="product-full-view">
  <img src="img/upload/<?= h($profile_image) ?>" alt="<?= h($product->name) ?>" width="300" height="300" loading="lazy">
  <p><strong>Price:</strong> $<?= number_format($product->price, 2) ?></p>
  <p><strong>Per:</strong> <?= h($product->amount_name) ?></p>
  <p><strong>Category:</strong> <?= h($product->category_name ?? 'Uncategorized') ?></p>
  <p><strong>Description:</strong> <?= nl2br(h($product->description)) ?></p>

  <?php
  $tags = Product::fetchTags($product_id);
  if (!empty($tags)): ?>
    <p><strong>Tags:</strong>
      <?php foreach ($tags as $tag): ?>
        <span class="tag"><?= h($tag) ?></span>
      <?php endforeach; ?>
    </p>
  <?php endif; ?>

  <p><strong>Sold by:</strong> <a href="vendor_profile.php?vendor_id=<?= h($vendor->vendor_id) ?>"><?= h($vendor->business_name) ?></a></p>
</div>

<?php require_once 'private/footer.php'; ?>
