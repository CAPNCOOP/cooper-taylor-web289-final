<?php

class User extends DatabaseObject
{
  protected static $primary_key = 'user_id';
  static protected $table_name = 'users';
  static protected $db_columns = ['username', 'password', 'email', 'first_name', 'last_name', 'user_level_id', 'is_active'];

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

  public function removeFavoriteVendor(int $vendor_id): bool
  {
    $sql = "DELETE FROM favorite WHERE user_id = ? AND vendor_id = ?";
    $stmt = self::$db->prepare($sql);
    return $stmt->execute([$this->user_id, $vendor_id]);
  }

  public static function toggleFavoriteVendor(int $user_id, int $vendor_id): bool|null
  {
    global $db;

    $sql = "SELECT 1 FROM favorite WHERE user_id = ? AND vendor_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$user_id, $vendor_id]);

    if ($stmt->fetch()) {
      // Remove favorite
      $sql = "DELETE FROM favorite WHERE user_id = ? AND vendor_id = ?";
      $stmt = $db->prepare($sql);
      return $stmt->execute([$user_id, $vendor_id]) ? false : null;
    } else {
      // Add favorite
      $sql = "INSERT INTO favorite (user_id, vendor_id) VALUES (?, ?)";
      $stmt = $db->prepare($sql);
      return $stmt->execute([$user_id, $vendor_id]) ? true : null;
    }
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
