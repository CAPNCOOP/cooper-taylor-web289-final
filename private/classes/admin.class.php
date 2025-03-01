<?php
class Admin
{
  protected $db;
  protected $user_id;

  public function __construct($db, $user_id)
  {
    $this->db = $db;
    $this->user_id = $user_id;
  }

  public function createVendor($name, $email, $password)
  {
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $query = "INSERT INTO users (first_name, email, password, user_level_id, is_active) 
                  VALUES (?, ?, ?, (SELECT user_level_id FROM user_level WHERE user_level_name = 'vendor'), 1)";
    $stmt = $this->db->prepare($query);
    return $stmt->execute([$name, $email, $hashedPassword]);
  }

  public function activateUser($userId)
  {
    $query = "UPDATE users SET is_active = 1 WHERE user_id = ?";
    $stmt = $this->db->prepare($query);
    return $stmt->execute([$userId]);
  }

  public function deactivateUser($userId)
  {
    $query = "UPDATE users SET is_active = 0 WHERE user_id = ?";
    $stmt = $this->db->prepare($query);
    return $stmt->execute([$userId]);
  }

  public function updateHomepageContent($newContent)
  {
    $query = "UPDATE site_settings SET homepage_content = ? WHERE id = 1";
    $stmt = $this->db->prepare($query);
    return $stmt->execute([$newContent]);
  }

  public function overrideVendorSchedule($vendorId, $marketDate)
  {
    $query = "UPDATE vendor_market SET status = 'confirmed' WHERE vendor_id = ? AND attend_date = ?";
    $stmt = $this->db->prepare($query);
    return $stmt->execute([$vendorId, $marketDate]);
  }
}
