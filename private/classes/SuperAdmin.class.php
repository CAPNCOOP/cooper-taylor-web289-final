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
   * Checks if the currently logged-in user is a super admin.
   *
   * @return bool True if user_level_id is 4, false otherwise.
   */
  public static function isSuperAdminLoggedIn(): bool
  {
    return isset($_SESSION['user_level_id']) && $_SESSION['user_level_id'] == 4;
  }
}
