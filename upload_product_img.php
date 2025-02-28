<?php
require_once 'private/initialize.php';
require_once 'private/header.php';
$page_title = "Upload - Product"; // Set dynamic title

// Ensure user is logged in and is a vendor
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit("Redirecting to login...");
}

// Fetch vendor ID
$user_id = $_SESSION['user_id'];
$sql = "SELECT vendor_id FROM vendor WHERE user_id = ? AND vendor_status = 'approved'";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id]);
$vendor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vendor) {
  header("Location: index.php");
  exit("Access denied: Vendor approval required.");
}
$vendor_id = $vendor['vendor_id'];

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['product_image']) && isset($_POST['product_id'])) {
  $product_id = $_POST['product_id'];
  $upload_dir = 'img/upload/products/';
  $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
  $file = $_FILES['product_image'];
  $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
  $new_filename = "product_{$product_id}.{$file_ext}";
  $target_path = $upload_dir . $new_filename;

  if (in_array($file['type'], $allowed_types) && $file['size'] <= 5000000) { // Limit size to 5MB
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
      // Resize image
      list($width, $height) = getimagesize($target_path);
      $new_width = 500;
      $new_height = 500;
      $image_resized = imagecreatetruecolor($new_width, $new_height);

      switch ($file['type']) {
        case 'image/jpeg':
          $image = imagecreatefromjpeg($target_path);
          break;
        case 'image/png':
          $image = imagecreatefrompng($target_path);
          break;
        case 'image/webp':
          $image = imagecreatefromwebp($target_path);
          break;
        default:
          $image = null;
      }

      if ($image) {
        imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

        switch ($file['type']) {
          case 'image/jpeg':
            imagejpeg($image_resized, $target_path, 90);
            break;
          case 'image/png':
            imagepng($image_resized, $target_path);
            break;
          case 'image/webp':
            imagewebp($image_resized, $target_path);
            break;
        }
        imagedestroy($image);
        imagedestroy($image_resized);
      }
      // Resize image
      list($width, $height) = getimagesize($target_path);
      $new_width = 500;
      $new_height = 500;
      $image_resized = imagecreatetruecolor($new_width, $new_height);

      switch ($file['type']) {
        case 'image/jpeg':
          $image = imagecreatefromjpeg($target_path);
          break;
        case 'image/png':
          $image = imagecreatefrompng($target_path);
          break;
        case 'image/webp':
          $image = imagecreatefromwebp($target_path);
          break;
        default:
          $image = null;
      }

      if ($image) {
        imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

        switch ($file['type']) {
          case 'image/jpeg':
            imagejpeg($image_resized, $target_path, 90);
            break;
          case 'image/png':
            imagepng($image_resized, $target_path);
            break;
          case 'image/webp':
            imagewebp($image_resized, $target_path);
            break;
        }
        imagedestroy($image);
        imagedestroy($image_resized);
      }
      // Save to database
      $sql = "INSERT INTO product_image (product_id, file_path) VALUES (?, ?) 
                    ON DUPLICATE KEY UPDATE file_path = VALUES(file_path)";
      $stmt = $db->prepare($sql);
      $stmt->execute([$product_id, $new_filename]);

      header("Location: manage_products.php?success=1");
      exit;
    } else {
      $error = "Error uploading file.";
    }
  } else {
    $error = "Invalid file type or size exceeds limit.";
  }
}

// Fetch vendor's products
$sql = "SELECT product_id, name FROM product WHERE vendor_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$vendor_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<body>
  <h1>Upload Product Image</h1>
  <?php if (isset($error)): ?>
    <p style="color: red;"> <?php echo htmlspecialchars($error); ?> </p>
  <?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <label for="product_id">Select Product:</label>
    <select name="product_id" required>
      <?php foreach ($products as $product): ?>
        <option value="<?php echo $product['product_id']; ?>"> <?php echo htmlspecialchars($product['name']); ?> </option>
      <?php endforeach; ?>
    </select><br>
    <input type="file" name="product_image" accept="image/*" required>
    <button type="submit">Upload</button>
  </form>
</body>

</html>
