<?php
$page_title = "Manage Products";
require_once 'private/initialize.php';
require_once 'private/header.php';

require_login(); // Ensure user is logged in

// Ensure user is a vendor
if ($_SESSION['user_level_id'] != 2) {
  $_SESSION['message'] = "❌ Access Denied: You must be a vendor.";
  header("Location: index.php");
  exit;
}

// Fetch vendor details
$user_id = $_SESSION['user_id'];
$sql = "SELECT vendor_id, business_name, vendor_status FROM vendor WHERE user_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id]);
$vendor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vendor) {
  $_SESSION['message'] = "❌ Error: Vendor profile not found.";
  header("Location: index.php");
  exit;
}

if ($vendor['vendor_status'] !== 'approved') {
  $_SESSION['message'] = "❌ Access Denied: Vendor approval required.";
  header("Location: index.php");
  exit;
}

$vendor_id = $vendor['vendor_id'];

// Fetch available amounts
$sql = "SELECT amount_id, amount_name FROM amount_offered";
$stmt = $db->query($sql);
$amounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once 'private/popup_message.php';

// Handle Product Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $product_name = h($_POST['product_name']);
  $price = h($_POST['price']);
  $amount_id = h($_POST['amount_id']);
  $description = h($_POST['description']);
  $custom_tags = strtolower(trim($_POST['custom_tags'] ?? ''));

  if (empty($product_name) || empty($price) || empty($amount_id) || empty($description)) {
    $_SESSION['message'] = "❌ Error: All fields must be filled.";
    header("Location: manage_products.php");
    exit;
  }

  // Insert product
  $sql = "INSERT INTO product (vendor_id, name, price, amount_id, description) VALUES (?, ?, ?, ?, ?)";
  $stmt = $db->prepare($sql);
  $stmt->execute([$vendor_id, $product_name, $price, $amount_id, $description]);

  $product_id = $db->lastInsertId();

  // Handle product image upload
  $product_image = 'img/upload/products/default_product.png';
  if (!empty($_FILES['product_image']['name'])) {
    $product_image = upload_image($_FILES['product_image'], 'products', $product_name);

    if (!$product_image) {
      $_SESSION['message'] = "❌ Error: Image upload failed.";
      header("Location: manage_products.php");
      exit;
    }

    // Ensure single image update or insert
    $sql = "INSERT INTO product_image (product_id, file_path) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE file_path = VALUES(file_path)";
    $stmt = $db->prepare($sql);
    $stmt->execute([$product_id, $product_image]);
  }

  // Handle custom tags (comma-separated)
  if (!empty($custom_tags)) {
    $custom_tags_array = array_map('trim', explode(',', $custom_tags));

    foreach ($custom_tags_array as $tag_name) {
      if (!empty($tag_name)) {
        // Check if tag already exists
        $sql = "SELECT tag_id FROM product_tag WHERE tag_name = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$tag_name]);
        $tag = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tag) {
          $sql = "INSERT INTO product_tag (tag_name) VALUES (?)";
          $stmt = $db->prepare($sql);
          $stmt->execute([$tag_name]);
          $tag_id = $db->lastInsertId();
        } else {
          $tag_id = $tag['tag_id'];
        }

        // Link tag to product
        $sql = "INSERT INTO product_tag_map (product_id, tag_id) VALUES (?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$product_id, $tag_id]);
      }
    }
  }

  header("Location: manage_products.php?message=product_added");
  exit;
}

// Fetch Vendor’s Products
$sql = "SELECT p.product_id, p.name, p.price, p.amount_id, a.amount_name, p.description, pi.file_path 
        FROM product p
        LEFT JOIN product_image pi ON p.product_id = pi.product_id
        LEFT JOIN amount_offered a ON p.amount_id = a.amount_id
        WHERE p.vendor_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$vendor_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Manage Products</h2>
<p>Business: <?= htmlspecialchars($vendor['business_name']); ?></p>

<!-- Product Upload Form -->
<form action="manage_products.php" method="POST" enctype="multipart/form-data">
  <legend>Add New Product</legend>

  <fieldset>
    <label for="product_name">Product Name:</label>
    <input type="text" id="product_name" name="product_name" required>
  </fieldset>

  <fieldset>
    <label for="price">Price ($):</label>
    <input type="number" step="0.01" id="price" name="price" required>
  </fieldset>

  <fieldset>
    <label for="amount_id">Select Amount:</label>
    <select id="amount_id" name="amount_id" required>
      <?php foreach ($amounts as $amount): ?>
        <option value="<?= $amount['amount_id']; ?>"><?= htmlspecialchars($amount['amount_name']); ?></option>
      <?php endforeach; ?>
    </select>
  </fieldset>

  <fieldset>
    <label for="description">Description:</label>
    <textarea id="description" name="description" required></textarea>
  </fieldset>

  <fieldset>
    <label for="custom_tags">Tags (comma-separated):</label>
    <input type="text" id="custom_tags" name="custom_tags" placeholder="e.g., fresh, organic, handmade">
  </fieldset>

  <fieldset>
    <label for="product_image">Product Image:</label>
    <input type="file" id="product_image" name="product_image" accept="image/png, image/jpeg, image/webp">
  </fieldset>

  <button type="submit">Add Product</button>
</form>

<div class="product-list">
  <?php if (!empty($products)): ?>
    <?php foreach ($products as $product): ?>
      <div class="product-card">
        <h3><?= htmlspecialchars($product['name']); ?></h3>
        <img src="img/upload/products/<?= htmlspecialchars($product['file_path'] ?? 'default_product.png'); ?>" height="150" width="150" alt="Product Image">
        <p><strong>Price:</strong> $<?= number_format($product['price'], 2); ?></p>
        <p><strong>Per:</strong> <?= htmlspecialchars($product['amount_name'] ?? 'unit'); ?></p>
        <p><?= htmlspecialchars($product['description']); ?></p>
        <a href="edit_product.php?id=<?= $product['product_id']; ?>" class="edit-btn">Update</a>
        <a href="delete_product.php?id=<?= $product['product_id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p>No products added yet.</p>
  <?php endif; ?>
</div>

<?php require_once 'private/footer.php'; ?>
