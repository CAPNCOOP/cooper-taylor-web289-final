<?php

class User extends DatabaseObject
{
  protected static $primary_key = 'user_id';
  static protected $table_name = 'users';
  static protected $db_columns = ['user_id', 'username', 'password', 'email', 'first_name', 'last_name', 'user_level_id', 'is_active'];

  public $user_id;
  public $username;
  public $password;
  public $email;
  public $first_name;
  public $last_name;
  public $user_level_id;
  public $is_active = 1;

  public function __construct($args = [])
  {
    $this->user_id = $args['user_id'] ?? 0;
    $this->username = $args['username'] ?? '';
    $this->password = $args['password'] ?? '';
    $this->email = $args['email'] ?? '';
    $this->first_name = $args['first_name'] ?? '';
    $this->last_name = $args['last_name'] ?? '';
    $this->user_level_id = $args['user_level_id'] ?? 0;
    $this->is_active = $args['is_active'] ?? 1;
  }

  function statusLabel(): string
  {
    return $this->is_active ? 'Active' : 'Inactive';
  }

  public static function isUsernameTaken(string $username, int $exclude_id = 0): bool
  {
    $db = static::getDatabase();
    $sql = "SELECT COUNT(*) FROM users WHERE username = ?";
    $params = [$username];

    if ($exclude_id > 0) {
      $sql .= " AND user_id != ?";
      $params[] = $exclude_id;
    }

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn() > 0;
  }

  public static function isEmailTaken(string $email, int $exclude_id = 0): bool
  {
    $db = static::getDatabase();
    $sql = "SELECT COUNT(*) FROM users WHERE email = ?";
    $params = [$email];

    if ($exclude_id > 0) {
      $sql .= " AND user_id != ?";
      $params[] = $exclude_id;
    }

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn() > 0;
  }

  public function getImagePath(): string
  {
    $db = static::getDatabase();

    $sql = "SELECT file_path FROM profile_image WHERE user_id = ? LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute([$this->user_id]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['file_path'] ?? 'default_user.png';
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
