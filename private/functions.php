<?php
function url_for($script_path)
{
  // add the leading '/' if not present
  if ($script_path[0] != '/') {
    $script_path = "/" . $script_path;
  }
  return WWW_ROOT . $script_path;
}

function u($string = "")
{
  return urlencode($string);
}

function raw_u($string = "")
{
  return rawurlencode($string);
}

function h($string = "")
{
  return htmlspecialchars($string ?? "", ENT_QUOTES, 'UTF-8');
}

function error_404()
{
  header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
  exit();
}

function error_500()
{
  header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
  exit();
}

function redirect_to($location)
{
  if (!headers_sent()) {
    header("Location: " . $location);
    exit();
  }
}

function is_post_request()
{
  return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function is_get_request()
{
  return $_SERVER['REQUEST_METHOD'] === 'GET';
}

function upload_image($file, $folder, $name)
{
  $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
  $upload_dir = __DIR__ . "/../img/upload/{$folder}/";

  if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true); // Create folder if it doesn't exist
  }

  if ($file['error'] === UPLOAD_ERR_OK) {
    $file_type = mime_content_type($file['tmp_name']);
    if (!in_array($file_type, $allowed_types)) {
      return null; // Invalid file type
    }

    // Force rename based on condition (User or Product)
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $sanitized_name = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $name)); // Sanitize

    $new_filename = "{$sanitized_name}.{$ext}"; // FINAL name format
    $target_path = $upload_dir . $new_filename;

    // Crop Image to 500x500 (Centered Crop)
    $image = imagecreatefromstring(file_get_contents($file['tmp_name']));
    $width = imagesx($image);
    $height = imagesy($image);
    $size = min($width, $height);
    $cropped_image = imagecrop($image, [
      'x' => ($width - $size) / 2,
      'y' => ($height - $size) / 2,
      'width' => 500,
      'height' => 500
    ]);

    switch ($file_type) {
      case 'image/jpeg':
        imagejpeg($cropped_image, $target_path);
        break;
      case 'image/png':
        imagepng($cropped_image, $target_path);
        break;
      case 'image/webp':
        imagewebp($cropped_image, $target_path);
        break;
    }

    imagedestroy($image);
    imagedestroy($cropped_image);

    return $new_filename;
  }
  return null;
}

// this function is used to get the existing tags of a product
function get_existing_tags($product_id)
{
  global $db;
  $sql = "SELECT t.tag_name FROM product_tag t
          JOIN product_tag_map ptm ON t.tag_id = ptm.tag_id
          WHERE ptm.product_id = ?";
  $stmt = $db->prepare($sql);
  $stmt->execute([$product_id]);
  return $stmt->fetchAll(PDO::FETCH_COLUMN);
}
