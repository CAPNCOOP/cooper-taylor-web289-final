<?php
$page_title = "Edit Product";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_once 'private/functions.php';
require_login();

// Ensure only vendors can access
if (!isset($_SESSION['user_id']) || $_SESSION['user_level_id'] != 2) {
  header("Location: index.php?message=error_unauthorized");
  exit();
}

// Fetch vendor_id and product details in a **single** query
$sql = "SELECT v.vendor_id, p.name, p.price, p.amount_id, p.description 
        FROM vendor v
        JOIN product p ON v.vendor_id = p.vendor_id
        WHERE v.user_id = ? AND p.product_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$_SESSION['user_id'], $_GET['id'] ?? null]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
  header("Location: manage_products.php?message=error_product_not_found");
  exit();
}

$product_id = h($_GET['id']);
$product_tags = get_existing_tags($product_id);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $product_name = h($_POST['product_name']);
  $price = h($_POST['price']);
  $amount_id = h($_POST['amount_id']);
  $description = h($_POST['description']);

  if (empty($product_name) || empty($price) || empty($description)) {
    header("Location: edit_product.php?id=$product_id&message=error_empty_fields");
    exit();
  }

  $db->beginTransaction();
  try {
    // ✅ Update product details
    $sql = "UPDATE product SET name = ?, price = ?, amount_id = ?, description = ? 
                WHERE product_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$product_name, $price, $amount_id, $description, $product_id]);

    // ✅ Process Image Upload
    if (!empty($_FILES['product_image']['name'])) {
      $product_image = upload_image($_FILES['product_image'], 'products', $product_name);
      if ($product_image) {
        $sql = "INSERT INTO product_image (product_id, file_path) VALUES (?, ?) 
                        ON DUPLICATE KEY UPDATE file_path = VALUES(file_path)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$product_id, $product_image]);
      } else {
        throw new Exception("error_upload");
      }
    }

    // ✅ Handle Product Tags only if new tags exist
    if (!empty($_POST['tags'])) {
      $tags = array_map('trim', explode(',', strtolower($_POST['tags'])));
      $sql = "DELETE FROM product_tag_map WHERE product_id = ?";
      $stmt = $db->prepare($sql);
      $stmt->execute([$product_id]);

      foreach ($tags as $tag) {
        if (!empty($tag)) {
          $sql = "SELECT tag_id FROM product_tag WHERE tag_name = ?";
          $stmt = $db->prepare($sql);
          $stmt->execute([$tag]);
          $existing_tag = $stmt->fetch(PDO::FETCH_ASSOC);

          $tag_id = $existing_tag['tag_id'] ?? null;
          if (!$tag_id) {
            $sql = "INSERT INTO product_tag (tag_name) VALUES (?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$tag]);
            $tag_id = $db->lastInsertId();
          }

          $sql = "INSERT INTO product_tag_map (product_id, tag_id) VALUES (?, ?)";
          $stmt = $db->prepare($sql);
          $stmt->execute([$product_id, $tag_id]);
        }
      }
    }

    // ✅ Commit Changes
    $db->commit();
    header("Location: manage_products.php?message=product_updated");
    exit();
  } catch (Exception $e) {
    $db->rollBack();
    header("Location: edit_product.php?id=$product_id&message=" . $e->getMessage());
    exit();
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
