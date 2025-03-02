<?php
$page_title = "Upload - Profile";
require_once 'private/initialize.php';
require_once 'private/header.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit("Redirecting to login...");
}

$user_id = $_SESSION['user_id'];

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
  $upload_dir = 'img/upload/users/';
  $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
  $file = $_FILES['profile_image'];
  $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
  $new_filename = "user_{$user_id}.{$file_ext}";
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

      // Save to database
      $sql = "INSERT INTO profile_image (user_id, file_path) VALUES (?, ?) 
                    ON DUPLICATE KEY UPDATE file_path = VALUES(file_path)";
      $stmt = $db->prepare($sql);
      $stmt->execute([$user_id, $new_filename]);

      header("Location: update_profile.php?success=1");
      exit;
    } else {
      $error = "Error uploading file.";
    }
  } else {
    $error = "Invalid file type or size exceeds limit.";
  }
}
?>

<h1>Upload Profile Image</h1>
<?php if (isset($error)): ?>
  <p style="color: red;"> <?php echo htmlspecialchars($error); ?> </p>
<?php endif; ?>
<form method="post" enctype="multipart/form-data">
  <input type="file" name="profile_image" accept="image/*" required>
  <button type="submit">Upload</button>
</form>
</body>

<?php require_once 'private/footer.php'; ?>
