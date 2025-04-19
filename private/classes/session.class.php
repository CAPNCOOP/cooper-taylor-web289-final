<?php
class Session
{
  private $user_id;
  public $username;
  private $last_login;
  private $user_level;

  public const MAX_LOGIN_AGE = 60 * 60 * 24; // 1 day

  /**
   * Initializes the session object by checking for stored session values.
   */
  public function __construct()
  {
    $this->check_stored_login();
  }

  /**
   * Logs in the given user and stores credentials in the session.
   *
   * @param array $user Associative array containing user_id, username, and user_level.
   * @return bool Always returns true after setting session data.
   */
  public function login($user)
  {
    if ($user) {
      $_SESSION['user_id'] = $user['user_id']; // FIX: Accessing user_id as array
      $_SESSION['username'] = $user['username'];
      $_SESSION['last_login'] = time();
      $_SESSION['user_level'] = $user['user_level'];

      $this->user_id = $_SESSION['user_id'];
      $this->username = $_SESSION['username'];
      $this->last_login = $_SESSION['last_login'];
      $this->user_level = $_SESSION['user_level'];
    }
    return true;
  }

  /**
   * Checks whether a user is currently logged in.
   *
   * @return bool True if a user session exists, false otherwise.
   */
  public static function is_logged_in()
  {
    return isset($_SESSION['user_id']);
  }

  /**
   * Redirects to the login page if no user is logged in.
   *
   * @return void
   */
  public static function require_login(): void
  {
    if (!self::is_logged_in()) {
      redirect_to('login.php');
    }
  }

  /**
   * Redirects to index if user is not logged in or is not a member.
   *
   * @return void
   */
  public static function require_member()
  {
    if (!self::is_logged_in() || $_SESSION['user_level_id'] != 1) {
      redirect_to('index.php');
      exit();
    }
  }

  /**
   * Redirects to index if user is not logged in or is not a vendor.
   *
   * @return void
   */
  public static function require_vendor()
  {
    if (!isset($_SESSION['user_level_id']) || $_SESSION['user_level_id'] != 2) {
      header("Location: index.php");
      exit;
    }
  }

  /**
   * Redirects to index if user is not logged in or is not an admin.
   *
   * @return void
   */
  public static function require_admin()
  {
    if (!isset($_SESSION['user_level_id']) || $_SESSION['user_level_id'] != 3) {
      header("Location: index.php");
      exit;
    }
  }

  /**
   * Redirects to index if user is not logged in or is not a super admin.
   *
   * @return void
   */
  public static function require_superadmin()
  {
    if (!isset($_SESSION['user_level_id']) || $_SESSION['user_level_id'] != 4) {
      header("Location: index.php");
      exit;
    }
  }

  /**
   * Checks if the current user is a vendor.
   *
   * @return bool True if user_level_id is 2.
   */
  public static function is_vendor(): bool
  {
    return isset($_SESSION['user_level_id']) && $_SESSION['user_level_id'] == 2;
  }

  /**
   * Returns the current logged-in user's ID.
   *
   * @return int|null User ID if set, or null.
   */
  public static function user_id(): ?int
  {
    return $_SESSION['user_id'] ?? null;
  }

  /**
   * Returns the user level of the logged-in user.
   *
   * @return int|null User level ID if set, or null.
   */
  public static function user_level_id(): ?int
  {
    return $_SESSION['user_level_id'] ?? null;
  }

  /**
   * Logs out the current user and clears session and local properties.
   *
   * @return bool Always returns true.
   */
  public function logout()
  {
    unset($_SESSION['user_id']);
    unset($_SESSION['username']);
    unset($_SESSION['last_login']);
    unset($_SESSION['user_level']); // FIX: Unset user_level
    unset($this->user_id);
    unset($this->username);
    unset($this->last_login);
    unset($this->user_level); // FIX: Unset user_level in class
    return true;
  }

  /**
   * Loads session values into class properties if a user session exists.
   *
   * @return void
   */
  private function check_stored_login()
  {
    if (isset($_SESSION['user_id'])) {
      $this->user_id = $_SESSION['user_id'];
      $this->username = $_SESSION['username'] ?? null;
      $this->user_level = $_SESSION['user_level'] ?? null;
      $this->last_login = $_SESSION['last_login'] ?? time(); // Set last login if missing
    } else {
      // Explicitly set everything to null if not found
      $this->user_id = null;
      $this->username = null;
      $this->user_level = null;
      $this->last_login = null;
    }
  }

  /**
   * Sets or gets a flash message stored in the session.
   *
   * @param string $msg Optional message to store.
   * @return string|bool Stored message if getting, true if setting.
   */
  public function message($msg = "")
  {
    if (!empty($msg)) {
      // Then this is a "set" message
      $_SESSION['message'] = $msg;
      return true;
    } else {
      // Then this is a "get" message
      return $_SESSION['message'] ?? '';
    }
  }

  /**
   * Clears the stored flash message from the session.
   *
   * @return void
   */
  public function clear_message()
  {
    unset($_SESSION['message']);
  }
}
