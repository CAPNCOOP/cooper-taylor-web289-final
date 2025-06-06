<?php
$page_title = "Edit Product";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_once 'private/functions.php';
require_once 'private/popup_message.php';
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
require_login();

if (!isset($_SESSION['user_id']) || $_SESSION['user_level_id'] != 2) {
  redirect_to('index.php');
}

// Fetch vendor_id for current user
$vendor = Vendor::find_by_user_id($_SESSION['user_id']);
$vendor_id = $vendor?->vendor_id ?? null;

if (!$vendor_id || !isset($_GET['id'])) {
  $session->message("❌ Error: Invalid product selected.");
  redirect_to('manage_products.php');
  exit;
}

$product_id = (int) $_GET['id'];
$product = Product::find_by_id($product_id);

// Get existing product image
$sql = "SELECT file_path FROM product_image WHERE product_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$product_id]);
$image_result = $stmt->fetch(PDO::FETCH_ASSOC);
$current_image = $image_result['file_path'] ?? 'products/default_product.webp';

if (!$product || $product->vendor_id != $vendor_id) {
  $session->message("❌ Error: You do not have permission to edit this product.");
  redirect_to('manage_products.php');
  exit;
}

// Fetch existing product tags
$product_tags = get_existing_tags($product_id);

// Fetch categories for dropdown
$sql = "SELECT category_id, category_name FROM category ORDER BY category_name ASC";
$stmt = $db->query($sql);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (is_post_request()) {
  $product->name = strip_tags(trim($_POST['product_name']));
  $product->price = trim($_POST['price']);
  $product->amount_id = trim($_POST['amount_id']);
  $product->category_id = trim($_POST['category_id']);
  $product->description = strip_tags(trim($_POST['description']));

  if (empty($product->name) || empty($product->price) || empty($product->description)) {
    $_SESSION['form_data'] = $_POST;
    $session->message("❌ Error: All fields must be filled.");
    redirect_to("edit_product.php?id=$product_id");
    exit;
  }

  $product->product_id = $product_id;
  $product->save();

  $product_file = handle_cropped_upload('cropped-product', 'products', $product->name, $product_id);

  if (!$product_file && !empty($_FILES['product_image']['name'])) {
    $product_file = upload_image($_FILES['product_image'], 'products', $product->name, $product_id);
  }

  if (!$product_file && !empty($_FILES['product_image']['name'])) {
    $_SESSION['form_data'] = $_POST;
    $session->message("❌ Invalid image type. Please upload JPG, PNG, or WebP.");
    redirect_to("edit_product.php?id=$product_id");
    exit;
  }

  if ($product_file) {
    $product_image_path = 'products/' . $product_file;

    if (!empty($current_image) && $current_image !== 'products/default_product.webp' && $current_image !== $product_image_path) {
      $old_path = __DIR__ . "/img/upload/" . $current_image;
      if (file_exists($old_path)) {
        unlink($old_path);
      }
    }

    $sql = "INSERT INTO product_image (product_id, file_path)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE file_path = VALUES(file_path)";
    $stmt = $db->prepare($sql);
    $stmt->execute([$product_id, $product_image_path]);
  } elseif (!$product_file && !empty($current_image) && $product->name !== basename($current_image, ".webp")) {
    $ext = pathinfo($current_image, PATHINFO_EXTENSION);
    $sanitized_name = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $product->name));
    $new_filename = "products/{$sanitized_name}_{$product_id}.{$ext}";
    $new_path = __DIR__ . "/img/upload/" . $new_filename;
    $old_path = __DIR__ . "/img/upload/" . $current_image;

    if (file_exists($old_path)) {
      rename($old_path, $new_path);

      $sql = "UPDATE product_image SET file_path = ? WHERE product_id = ?";
      $stmt = $db->prepare($sql);
      $stmt->execute([$new_filename, $product_id]);
    }
  }

  if (!empty($_POST['tags'])) {
    $tags = explode(',', strip_tags(strtolower($_POST['tags'] ?? '')));

    $sql = "DELETE FROM product_tag_map WHERE product_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$product_id]);

    foreach ($tags as $tag) {
      $tag = trim($tag);
      if (!$tag) continue;

      $sql = "SELECT tag_id FROM product_tag WHERE tag_name = ?";
      $stmt = $db->prepare($sql);
      $stmt->execute([$tag]);
      $existing_tag = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($existing_tag) {
        $tag_id = $existing_tag['tag_id'];
      } else {
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

  $session->message("✅ Product updated successfully!");
  redirect_to("manage_products.php");
  exit;
}
?>

<h2>Edit Product</h2>
<form action="edit_product.php?id=<?= $product_id ?>" method="POST" enctype="multipart/form-data">
  <div>
    <fieldset>
      <label for="product_name">Product Name:</label>
      <input type="text" id="product_name" name="product_name" value="<?= h($form_data['product_name'] ?? $product->name) ?>" required>
    </fieldset>

    <fieldset>
      <label for="price">Price ($):</label>
      <input type="number" step="0.01" id="price" name="price" value="<?= h($form_data['price'] ?? $product->price) ?>" required>
    </fieldset>

    <fieldset>
      <label for="amount_id">Amount Offered:</label>
      <select id="amount_id" name="amount_id">
        <?php
        $selected_amount_id = $form_data['amount_id'] ?? $product->amount_id;
        $amount_sql = "SELECT * FROM amount_offered";
        $amount_stmt = $db->query($amount_sql);
        while ($amount = $amount_stmt->fetch(PDO::FETCH_ASSOC)) {
          $selected = ($amount['amount_id'] == $selected_amount_id) ? "selected" : "";
          echo "<option value='{$amount['amount_id']}' $selected>" . h($amount['amount_name']) . "</option>";
        }
        ?>
      </select>
    </fieldset>

    <fieldset>
      <label for="description">Description:</label>
      <textarea id="description" name="description" spellcheck="true"><?= h($form_data['description'] ?? $product->description) ?></textarea>
    </fieldset>

    <fieldset>
      <label for="category_id">Category:</label>
      <select id="category_id" name="category_id" required>
        <option value="" disabled <?= empty($product->category_id) ? 'selected' : '' ?>>Select a Category</option>
        <?php foreach ($categories as $category): ?>
          <option value="<?= $category['category_id'] ?>"
            <?= ($category['category_id'] == $product->category_id) ? 'selected' : '' ?>>
            <?= h(ucwords(str_replace('_', ' ', $category['category_name']))) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </fieldset>

    <fieldset>
      <label for="tags">Tags (comma-separated):</label>
      <input type="text" id="tags" name="tags" value="<?= h($form_data['tags'] ?? implode(', ', $product_tags)) ?>" spellcheck="true">
    </fieldset>
  </div>

  <div>
    <fieldset>
      <legend>Edit Product Image</legend>

      <div class="image-upload-wrapper">
        <img
          src="img/upload/<?= h($current_image ?? 'products/default_product.webp') ?>"
          alt="Current product preview"
          id="product-preview"
          class="image-preview"
          width="200"
          height="200"
          loading="lazy">

        <label for="product-image" class="upload-label" aria-label="Edit Product Image">
          Choose New Image
        </label>

        <input
          type="file"
          name="product_image"
          id="product-image"
          class="image-input"
          accept="image/png, image/jpeg, image/webp"
          aria-describedby="product-image-desc"
          onchange="previewImage(event)">

        <p id="product-image-desc" class="visually-hidden">
          Upload a JPG, PNG, or WebP product image.
        </p>
      </div>

      <!-- Cropper Modal -->
      <div id="cropper-modal" style="display: none;">
        <div id="cropper-modal-inner">
          <img
            id="cropper-image"
            src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw=="
            alt="Product image crop preview"
            style="display: none;">
        </div>
        <button type="button" id="crop-confirm">Crop & Upload</button>
      </div>

      <input type="hidden" name="cropped-product" id="cropped-product">
      <button type="submit">Save Changes</button>
    </fieldset>
  </div>
</form>

<?php require_once 'private/footer.php'; ?>
