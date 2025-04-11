<?php
class Session
{

  private $user_id;
  public $username;
  private $last_login;
  private $user_level;

  public const MAX_LOGIN_AGE = 60 * 60 * 24; // 1 day

  public function __construct()
  {
    $this->check_stored_login();
  }

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

  public function is_logged_in()
  {
    return isset($this->user_id) && isset($this->last_login) && $this->last_login_is_recent();
  }

  public static function is_vendor(): bool
  {
    return isset($_SESSION['user_level_id']) && $_SESSION['user_level_id'] == 2;
  }

  public static function is_admin(): bool
  {
    return isset($_SESSION['user_level_id']) && $_SESSION['user_level_id'] == 1;
  }

  public static function is_super_admin(): bool
  {
    return isset($_SESSION['user_level_id']) && $_SESSION['user_level_id'] == 3;
  }

  public static function user_id(): ?int
  {
    return $_SESSION['user_id'] ?? null;
  }

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

  private function last_login_is_recent()
  {
    if (!isset($this->last_login)) {
      return false;
    } elseif (($this->last_login + self::MAX_LOGIN_AGE) < time()) {
      return false;
    } else {
      return true;
    }
  }

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

  public function clear_message()
  {
    unset($_SESSION['message']);
  }
}
