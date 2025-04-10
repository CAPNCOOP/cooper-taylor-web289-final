<?php
require_once 'Admin.class.php';

class SuperAdmin extends Admin
{
  // Fetch all admin users
  public function fetchAdmins()
  {
    return User::find_by_sql("SELECT * FROM users WHERE user_level_id = 3");
  }

  // Activate an admin user by ID
  public function activateAdmin($adminId): bool
  {
    $admin = self::find_by_id($adminId);
    if ($admin && $admin->user_level_id == 3) {
      $admin->is_active = 1;
      return $admin->save();
    }
    return false;
  }



  // Deactivate an admin user by ID
  public function deactivateAdmin($adminId): bool
  {
    $admin = self::find_by_id($adminId);
    if ($admin && $admin->user_level_id == 3) {
      $admin->is_active = 0;
      return $admin->save();
    }
    return false;
  }


  public static function isSuperAdminLoggedIn(): bool
  {
    return isset($_SESSION['user_level_id']) && $_SESSION['user_level_id'] == 4;
  }
}
