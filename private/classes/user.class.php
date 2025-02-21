<?php

class User extends DatabaseObject
{
  static protected $table_name = 'users';
  static protected $db_columns = ['user_id', 'username', 'password', 'email', 'first_name', 'last_name', 'user_level_id'];

  public $user_id;
  public $username;
  public $password;
  public $email;
  public $first_name;
  public $last_name;
  public $user_level_id;

  public function __construct($args = [])
  {
    $this->username = $args['username'] ?? '';
    $this->password = $args['password'] ?? '';
    $this->email = $args['email'] ?? '';
    $this->first_name = $args['first_name'] ?? '';
    $this->last_name = $args['last_name'] ?? '';
    $this->user_level_id = $args['user_level_id'] ?? 0;
  }
}

function find_by_username($username)
{
  global $db;
  $sql = "SELECT * FROM users WHERE username = ? LIMIT 1";
  $stmt = $db->prepare($sql);
  $stmt->execute([$username]);
  return $stmt->fetch(PDO::FETCH_ASSOC);
}
