<?php
ob_start();
session_set_cookie_params([
  'lifetime' => 36000, // 10 hours
  'path' => '/',
  'domain' => '', // Leave empty for default
  'secure' => false, // Set to true if using HTTPS
  'httponly' => true,
  'samesite' => 'Lax' // Adjust if necessary
]);

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Assign file paths to PHP constants
// __FILE__ returns the current path to this file
// dirname() returns the path to the parent directory
define("PRIVATE_PATH", dirname(__FILE__));
define("PROJECT_PATH", dirname(PRIVATE_PATH));
define("PUBLIC_PATH", PROJECT_PATH . '/public');
define("SHARED_PATH", PRIVATE_PATH . '/shared');

// Assign the root URL to a PHP constant
// * Do not need to include the domain
// * Use same document root as webserver
// * Can set a hardcoded value:
// define("WWW_ROOT", '/~kevinskoglund/chain_gang/public');
// define("WWW_ROOT", '');
// * Can dynamically find everything in URL up to "/public"
// $public_end = strpos($_SERVER['SCRIPT_NAME'], '') + 7;
// $doc_root = substr($_SERVER['SCRIPT_NAME'], 0, $public_end);
define("WWW_ROOT", '');
// $doc_root

require_once('functions.php');
require_once('status_error_functions.php');
require_once('db_credentials.php');
require_once('database_functions.php');
require_once('validation_functions.php');
require_once('cooper-taylor-db-connection.php');

/*
 * The function `my_autoload` dynamically loads PHP class files from a specified directory based on the
 * class name.
 * 
 * @param class The `class` parameter in the `my_autoload` function is a variable that represents the
 * class name that is being autoloaded. It is used to dynamically include the corresponding class file
 * based on the class name provided.
 */
function my_autoload($class)
{
  if (preg_match('/\A\w+\Z/', $class)) {
    include('classes/' . $class . '.class.php');
  }
}
spl_autoload_register('my_autoload');

$database = db_connect();
DatabaseObject::setDatabase($database); // 
$session = new Session(); // This ensures $session is available globally
