<?php
/*
 * The function `url_for` in PHP adds a leading '/' to the script path if it's not already present and
 * returns the full URL with the script path appended to the `WWW_ROOT`.
 * 
 * @param script_path The `script_path` parameter in the `url_for` function represents the path to a
 * specific script or resource on the website. This function is used to generate a full URL for a given
 * script path by appending it to the `WWW_ROOT` constant, which typically represents the base URL of
 * the website
 * 
 * @return The function `url_for` is returning the concatenation of the constant `WWW_ROOT` and the
 * `` parameter after ensuring that the `` has a leading '/' by adding it if
 * it's not present.
 */
function url_for($script_path)
{
  // add the leading '/' if not present
  if ($script_path[0] != '/') {
    $script_path = "/" . $script_path;
  }
  return WWW_ROOT . $script_path;
}

/*
 * The function `u` in PHP is used to encode a string using URL encoding.
 * 
 * @param string The `u` function is a simple function that takes a string as input (or an empty string
 * by default) and then returns the URL-encoded version of that string using the `urlencode` function
 * in PHP.
 * 
 * @return The `urlencode` function is being called with the input string ``, and the encoded
 * string is being returned.
 */

function u($string = "")
{
  return urlencode($string);
}

/*
 * The function `raw_u` in PHP returns the raw URL-encoded version of a given string.
 * 
 * @param string The `raw_u` function is a simple wrapper function that takes a string as input and
 * returns the raw URL-encoded version of that string using PHP's `rawurlencode` function. If you pass
 * a string to this function, it will be URL-encoded and returned. If you don't pass any string
 * 
 * @return The `raw_u` function is returning the raw URL-encoded version of the input string using the
 * `rawurlencode` function in PHP.
 */
function raw_u($string = "")
{
  return rawurlencode($string);
}

/*
 * The function `h()` in PHP is used to safely escape and encode HTML entities in a given string.
 * 
 * @param string The `h` function is a custom function that takes a string as input and returns the
 * HTML-escaped version of that string using the `htmlspecialchars` function. The function also has a
 * default parameter of an empty string in case no input is provided.
 * 
 * @return The function `h()` returns the input string after encoding it with `htmlspecialchars()`
 * function using the `ENT_QUOTES` flag and UTF-8 encoding. If the input string is not provided, an
 * empty string is used for encoding.
 */
function h($string = "")
{
  return htmlspecialchars($string ?? "", ENT_QUOTES, 'UTF-8');
}

/**
 * The function `error_404` sets the HTTP header to indicate a 404 Not Found error and exits the
 * script.
 */
function error_404()
{
  header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
  exit();
}

/*
 * The function `error_500` sets the HTTP header to indicate a 500 Internal Server Error and exits the
 * script.
 */
function error_500()
{
  header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
  exit();
}

/*
 * The function `redirect_to` is used to redirect the user to a specified location in PHP, ensuring
 * that headers are not sent before the redirection.
 * 
 * @param location The `redirect_to` function is used to redirect the user to a different location. The
 * parameter `` should be a string containing the URL where you want to redirect the user. For
 * example, you can pass a URL like "https://www.example.com/newpage.php" as the `
 */
function redirect_to($location)
{
  if (!headers_sent()) {
    header("Location: " . $location);
    exit();
  }
}

/*
 * The function is_post_request() checks if the current request method is POST in PHP.
 * 
 * @return The function `is_post_request()` is returning a boolean value based on whether the current
 * request method is 'POST'.
 */
function is_post_request()
{
  return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/*
 * The function `is_get_request()` checks if the current request method is a GET request in PHP.
 * 
 * @return The function `is_get_request()` is returning a boolean value based on whether the current
 * request method is a GET request or not.
 */
function is_get_request()
{
  return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/*
 * The function `upload_image` uploads and processes an image file, including validation, renaming, and
 * cropping, before saving it to a specified folder.
 * 
 * @param file The `upload_image` function you provided is used to upload and process images. It takes
 * three parameters:
 * @param folder The `folder` parameter in the `upload_image` function represents the directory within
 * the `upload` folder where the uploaded image will be stored. It is a string that specifies the
 * subfolder where the image will be saved. For example, if you pass `'user'` as the `folder`
 * @param name The `name` parameter in the `upload_image` function represents the name that will be
 * used to save the uploaded image file. This name is sanitized to remove any special characters and
 * spaces, and then combined with the file extension to form the final filename under which the image
 * will be stored in the specified
 * 
 * @return The function `upload_image` returns the new filename of the uploaded and cropped image if
 * the file upload was successful and the file type is valid. If the file upload encountered an error
 * or the file type is not allowed, it returns `null`.
 */
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


/*
 * The function `get_existing_tags` retrieves existing tag names associated with a product ID from the
 * database.
 * 
 * @param product_id The `get_existing_tags` function retrieves existing tags associated with a product
 * based on the provided `product_id`. The function executes a SQL query to select the tag names from
 * the `product_tag` table by joining it with the `product_tag_map` table on the `tag_id`. The tags are
 * 
 * @return The function `get_existing_tags()` returns an array of existing tag names
 * associated with the specified product ID. The tags are fetched from the database table `product_tag`
 * based on the product ID provided and returned as an array of tag names.
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
