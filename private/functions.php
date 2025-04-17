<?php

/**
 * Returns a full URL path by prepending WWW_ROOT to the given script path.
 *
 * @param string $script_path The relative script path (e.g., 'index.php').
 * @return string The full URL path.
 */
function url_for($script_path)
{
  // add the leading '/' if not present
  if ($script_path[0] != '/') {
    $script_path = "/" . $script_path;
  }
  return WWW_ROOT . $script_path;
}

/**
 * URL-encodes a string using `urlencode`.
 *
 * @param string $string The string to encode.
 * @return string The encoded string.
 */
function u($string = "")
{
  return urlencode($string);
}

/**
 * URL-encodes a string using `rawurlencode`.
 *
 * @param string $string The string to encode.
 * @return string The encoded string.
 */
function raw_u($string = "")
{
  return rawurlencode($string);
}

/**
 * Escapes HTML special characters in a string using `htmlspecialchars`.
 *
 * @param string $string The string to escape.
 * @return string The escaped string.
 */
function h($string = "")
{
  return htmlspecialchars($string ?? "", ENT_QUOTES, 'UTF-8');
}

/**
 * Sends a 404 Not Found header and exits.
 *
 * @return void
 */
function error_404()
{
  header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
  exit();
}

/**
 * Sends a 500 Internal Server Error header and exits.
 *
 * @return void
 */
function error_500()
{
  header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
  exit();
}

/**
 * Redirects the browser to a given location and exits.
 *
 * @param string $location The destination URL or path.
 * @return void
 */
function redirect_to($location)
{
  if (!headers_sent()) {
    header("Location: " . $location);
    exit();
  }
}

/**
 * Checks whether the current request method is POST.
 *
 * @return bool True if POST, false otherwise.
 */
function is_post_request()
{
  return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Checks whether the current request method is GET.
 *
 * @return bool True if GET, false otherwise.
 */
function is_get_request()
{
  return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**
 * Handles image upload with validation and centered crop to 500x500.
 *
 * @param array $file The uploaded file from $_FILES.
 * @param string $folder The target subdirectory under /img/upload/.
 * @param string $name The desired filename base (will be sanitized).
 * @return string|null The new filename if successful, or null on failure.
 */
function upload_image($file, $folder, $name, $id = null)
{
  $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
  $upload_dir = __DIR__ . "/../img/upload/{$folder}/";

  if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
  }

  if ($file['error'] === UPLOAD_ERR_OK) {
    $file_type = mime_content_type($file['tmp_name']);
    if (!in_array($file_type, $allowed_types)) {
      return null;
    }

    $ext = 'webp';
    $sanitized_name = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $name));

    $new_filename = $id !== null
      ? "{$sanitized_name}_{$id}.{$ext}"
      : "{$sanitized_name}.{$ext}";

    $target_path = $upload_dir . $new_filename;

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

    $save_success = imagewebp($cropped_image, $target_path); // ONLY once

    imagedestroy($image);
    imagedestroy($cropped_image);

    if (!$save_success) {
      error_log("❌ imagewebp failed to save: $target_path");
      return null;
    }

    $full_check = $upload_dir . $new_filename;
    if (!file_exists($full_check)) {
      error_log("❌ FILE WAS NOT SAVED: $full_check");
    } else {
      error_log("✅ FILE IS ON DISK: $full_check");
    }

    error_log("✅ Saved WebP image to: $target_path");
    return $new_filename;
  }

  return null;
}

/**
 * Handles base64-encoded image upload from Cropper.js
 *
 * @param string $field The POST key (e.g., 'cropped-image', 'cropped-product')
 * @param string $folder The subfolder to save into (e.g., 'users', 'products')
 * @param string $name The sanitized base name for the file
 * @return string|null The uploaded file name or null on failure
 */
function handle_cropped_upload(string $field, string $folder, string $name, $id = "null"): ?string
{
  if (!empty($_POST[$field])) {
    $image_parts = explode(',', $_POST[$field]);
    if (count($image_parts) === 2) {
      $decoded = base64_decode($image_parts[1]);
      $tmp = tempnam(sys_get_temp_dir(), 'crop_');
      file_put_contents($tmp, $decoded);

      $fake = [
        'name' => strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $name)) . '.webp',
        'tmp_name' => $tmp,
        'type' => 'image/webp',
        'error' => UPLOAD_ERR_OK,
      ];

      $file = upload_image($fake, $folder, $name, $id);
      unlink($tmp);
      return $file;
    }
  }
  return null;
}

/**
 * Retrieves all tags associated with a given product ID.
 *
 * @param int $product_id The product ID to look up.
 * @return array List of tag names.
 */
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

/**
 * Retrieves the file path to a user's profile image, or a default if not set.
 *
 * @param int $user_id The user ID.
 * @return string File path to the profile image.
 */
function get_profile_image($user_id)
{
  global $db;
  $sql = "SELECT file_path FROM profile_image WHERE user_id = ?";
  $stmt = $db->prepare($sql);
  $stmt->execute([$user_id]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  return $result ? $result['file_path'] : 'img/upload/users/default.png';
}
