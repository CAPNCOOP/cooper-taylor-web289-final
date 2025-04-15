<?php
require_once 'Admin.class.php';

class SuperAdmin extends Admin
{

  /**
   * Retrieves all super admin users.
   *
   * @return array Array of User objects with user_level_id = 3.
   */
  public function fetchAdmins()
  {
    return User::find_by_sql("SELECT * FROM users WHERE user_level_id = 3");
  }

  /**
   * Activates a super admin by setting is_active to true.
   *
   * @param int $adminId The user ID of the admin to activate.
   * @return bool True on success, false on failure.
   */
  public function activateAdmin($adminId): bool
  {
    $admin = self::find_by_id($adminId);
    if ($admin && $admin->user_level_id == 3) {
      $admin->is_active = 1;
      return $admin->save();
    }
    return false;
  }



  /**
   * Deactivates a super admin by setting is_active to false.
   *
   * @param int $adminId The user ID of the admin to deactivate.
   * @return bool True on success, false on failure.
   */
  public function deactivateAdmin($adminId): bool
  {
    $admin = self::find_by_id($adminId);
    if ($admin && $admin->user_level_id == 3) {
      $admin->is_active = 0;
      return $admin->save();
    }
    return false;
  }

  /**
   * Checks if the currently logged-in user is a super admin.
   *
   * @return bool True if user_level_id is 4, false otherwise.
   */
  public static function isSuperAdminLoggedIn(): bool
  {
    return isset($_SESSION['user_level_id']) && $_SESSION['user_level_id'] == 4;
  }
}
