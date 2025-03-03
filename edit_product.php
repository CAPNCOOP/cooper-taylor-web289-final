<?php
$page_title = "Edit Product";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_once 'private/functions.php';
require_login();

// Ensure only vendors can access
if (!isset($_SESSION['user_id']) || $_SESSION['user_level_id'] != 2) {
  header("Location: index.php");
  exit("Access Denied: You must be a vendor.");
}

// Fetch vendor_id
$sql = "SELECT vendor_id FROM vendor WHERE user_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$vendor = $stmt->fetch(PDO::FETCH_ASSOC);
$vendor_id = $vendor['vendor_id'];

// Check if product_id is set
if (!isset($_GET['id'])) {
  die("❌ No product selected.");
}
$product_id = h($_GET['id']);

// Fetch product details
$sql = "SELECT * FROM product WHERE product_id = ? AND vendor_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$product_id, $vendor_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
  die("❌ Product not found or you do not have permission to edit this.");
}

// Fetch existing product tags
$product_tags = get_existing_tags($product_id);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $product_name = h($_POST['product_name']);
  $price = h($_POST['price']);
  $amount_id = h($_POST['amount_id']);
  $description = h($_POST['description']);

  // Update product details
  $sql = "UPDATE product SET name = ?, price = ?, amount_id = ?, description = ? WHERE product_id = ? AND vendor_id = ?";
  $stmt = $db->prepare($sql);
  $stmt->execute([$product_name, $price, $amount_id, $description, $product_id, $vendor_id]);

  // Handle image upload (if a new image is provided)
  // Fetch current image BEFORE updating
  $sql = "SELECT file_path FROM product_image WHERE product_id = ?";
  $stmt = $db->prepare($sql);
  $stmt->execute([$product_id]);
  $current_image = $stmt->fetchColumn();

  // If a new image is uploaded, process it, otherwise retain the existing image
  if (!empty($_FILES['product_image']['name'])) {
    $product_image = upload_image($_FILES['product_image'], 'products', $product_name);
  } else {
    $product_image = $current_image; // Keep existing image
  }

  // Insert new image or update the existing one
  if (!empty($_FILES['product_image']['name'])) {
    $product_image = upload_image($_FILES['product_image'], 'products', $product_name);

    // Check if an image exists
    $sql = "SELECT COUNT(*) FROM product_image WHERE product_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$product_id]);
    $image_exists = $stmt->fetchColumn();

    if ($image_exists) {
      // ✅ Update only if image already exists
      $sql = "UPDATE product_image SET file_path = ? WHERE product_id = ?";
    } else {
      // ✅ Insert only if no existing image
      $sql = "INSERT INTO product_image (product_id, file_path) VALUES (?, ?)";
    }
    $stmt = $db->prepare($sql);
    $stmt->execute([$product_image, $product_id]);
  }



  // Handle product tags
  if (!empty($_POST['tags'])) {
    $tags = explode(',', strtolower($_POST['tags']));

    // Delete existing tags for the product
    $sql = "DELETE FROM product_tag_map WHERE product_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$product_id]);

    // Add new tags
    foreach ($tags as $tag) {
      $tag = trim($tag);

      // Check if tag exists
      $sql = "SELECT tag_id FROM product_tag WHERE tag_name = ?";
      $stmt = $db->prepare($sql);
      $stmt->execute([$tag]);
      $existing_tag = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($existing_tag) {
        $tag_id = $existing_tag['tag_id'];
      } else {
        // Insert new tag
        $sql = "INSERT INTO product_tag (tag_name) VALUES (?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$tag]);
        $tag_id = $db->lastInsertId();
      }

      // Check if this product already has this tag
      $sql = "SELECT COUNT(*) FROM product_tag_map WHERE product_id = ? AND tag_id = ?";
      $stmt = $db->prepare($sql);
      $stmt->execute([$product_id, $tag_id]);
      $tag_exists = $stmt->fetchColumn();

      if (!$tag_exists) {
        $sql = "INSERT INTO product_tag_map (product_id, tag_id) VALUES (?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$product_id, $tag_id]);
      }
    }
  }

  if ($stmt->rowCount() > 0) {
    header("Location: manage_products.php?success=product_updated");
    exit;
  } else {
    echo "⚠️ Warning: No changes were made.";
  }
}
?>

<h2>Edit Product</h2>
<form action="edit_product.php?id=<?= $product_id ?>" method="POST" enctype="multipart/form-data">
  <fieldset>
    <label for="product_name">Product Name:</label>
    <input type="text" id="product_name" name="product_name" value="<?= htmlspecialchars($product['name']) ?>" required>
  </fieldset>

  <fieldset>
    <label for="price">Price ($):</label>
    <input type="number" step="0.01" id="price" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>
  </fieldset>

  <fieldset>
    <label for="amount_id">Amount Offered:</label>
    <select id="amount_id" name="amount_id">
      <?php
      $amount_sql = "SELECT * FROM amount_offered";
      $amount_stmt = $db->query($amount_sql);
      while ($amount = $amount_stmt->fetch(PDO::FETCH_ASSOC)) {
        $selected = ($amount['amount_id'] == $product['amount_id']) ? "selected" : "";
        echo "<option value='{$amount['amount_id']}' $selected>" . htmlspecialchars($amount['amount_name']) . "</option>";
      }
      ?>
    </select>
  </fieldset>

  <fieldset>
    <label for="description">Description:</label>
    <textarea id="description" name="description" spellcheck="true"><?= htmlspecialchars($product['description']) ?></textarea>
  </fieldset>

  <fieldset>
    <label for="tags">Tags (comma-separated):</label>
    <input type="text" id="tags" name="tags" value="<?= htmlspecialchars(implode(', ', $product_tags)) ?>" spellcheck="true">
  </fieldset>

  <fieldset>
    <label for="product_image">Product Image:</label>
    <input type="file" id="product_image" name="product_image" accept="image/png, image/jpeg, image/webp">
  </fieldset>

  <button type="submit">Save Changes</button>
</form>

<?php require_once 'private/footer.php'; ?>
