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

// function redirect_to($location)
// {
//   if (!headers_sent()) {
//     header("Location: " . $location);
//     exit();
//   }
// }

function redirect_to($location)
{
  echo "Redirect function called for: $location <br>";
  flush();

  if (headers_sent($file, $line)) {
    die("❌ Headers already sent in $file on line $line. Cannot redirect.");
  }

  header("Location: " . $location);
  exit("🚀 Exit called in redirect_to().");
}

function is_post_request()
{
  return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function is_get_request()
{
  return $_SERVER['REQUEST_METHOD'] === 'GET';
}

// PHP on Windows does not have a money_format() function.
// This is a super-simple replacement.
if (!function_exists('money_format')) {
  function money_format($format, $number)
  {
    return '$' . number_format($number, 2);
  }
}
