<?php
require_once 'Admin.class.php';

class SuperAdmin extends Admin
{

  public function activateAdmin($adminId)
  {
    $query = "UPDATE users SET is_active = 1 WHERE user_id = ? 
                  AND user_level_id = (SELECT user_level_id FROM user_level WHERE user_level_name = 'admin')";
    $stmt = $this->db->prepare($query);
    return $stmt->execute([$adminId]);
  }

  public function deactivateAdmin($adminId)
  {
    $query = "UPDATE users SET is_active = 0 WHERE user_id = ? 
                  AND user_level_id = (SELECT user_level_id FROM user_level WHERE user_level_name = 'admin')";
    $stmt = $this->db->prepare($query);
    return $stmt->execute([$adminId]);
  }
}
