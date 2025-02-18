<?php

class Session
{

  private $member_id;
  public $username;
  private $last_login;
  private $user_level;

  public const MAX_LOGIN_AGE = 60 * 60 * 24; // 1 day

  public function __construct()
  {
    session_start();
    $this->check_stored_login();
  }

  public function login($member)
  {
    if ($member) {
      session_regenerate_id();
      $_SESSION['member_id'] = $member->id;
      $_SESSION['username'] = $member->username;
      $_SESSION['last_login'] = time();
      $_SESSION['user_level'] = $member->user_level;

      $this->member_id = $member->id;
      $this->username = $_SESSION['member_id'] = $member->username;
      $this->last_login = time();
      $this->user_level = $member->user_level;
    }
    return true;
  }

  public function is_logged_in()
  {
    // return isset($this->member_id);
    return isset($this->member_id) && $this->last_login_is_recent();
  }

  public function is_admin_logged_in()
  {
    return $this->is_logged_in() && $this->user_level == 'a';
  }

  public function logout()
  {
    unset($_SESSION['member_id']);
    unset($_SESSION['username']);
    unset($_SESSION['last_login']);
    unset($this->member_id);
    unset($this->username);
    unset($this->last_login);
    return true;
  }

  private function check_stored_login()
  {
    if (isset($_SESSION['member_id'])) {
      $this->member_id = $_SESSION['member_id'];
      $this->username = $_SESSION['username'] ?? NULL;
      $this->user_level = $_SESSION['user_level'] ?? NULL;
      $this->last_login = $_SESSION['last_login'] ?? NULL;
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
