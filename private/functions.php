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

function upload_image($file, $folder, $name_reference = "default")
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

    // **Sanitize Name Reference**
    $clean_name = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $name_reference)));

    // Get file extension
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

    // **Generate Final Filename**
    $new_filename = "{$clean_name}.{$ext}";
    $target_path = $upload_dir . $new_filename;

    // Load image based on type
    switch ($file_type) {
      case 'image/jpeg':
        $image = imagecreatefromjpeg($file['tmp_name']);
        break;
      case 'image/png':
        $image = imagecreatefrompng($file['tmp_name']);
        break;
      case 'image/webp':
        $image = imagecreatefromwebp($file['tmp_name']);
        break;
      default:
        return null; // Unsupported type
    }

    // Get original dimensions
    $orig_width = imagesx($image);
    $orig_height = imagesy($image);

    // Determine crop size (center-crop to square)
    $crop_size = min($orig_width, $orig_height);
    $crop_x = ($orig_width - $crop_size) / 2;
    $crop_y = ($orig_height - $crop_size) / 2;

    // Create a blank square canvas (500x500)
    $cropped_image = imagecreatetruecolor(500, 500);
    imagecopyresampled(
      $cropped_image,
      $image,
      0,
      0,
      $crop_x,
      $crop_y,
      500,
      500,
      $crop_size,
      $crop_size
    );

    // Save cropped image based on type
    switch ($file_type) {
      case 'image/jpeg':
        imagejpeg($cropped_image, $target_path, 90);
        break;
      case 'image/png':
        imagepng($cropped_image, $target_path);
        break;
      case 'image/webp':
        imagewebp($cropped_image, $target_path);
        break;
    }

    // Cleanup
    imagedestroy($image);
    imagedestroy($cropped_image);

    return "img/upload/{$folder}/" . $new_filename;
  }

  return null;
}
