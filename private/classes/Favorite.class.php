<?php

class Favorite extends DatabaseObject
{
  /**
   * Checks if the 'favorite' table exists in the database.
   *
   * @return bool True if the table exists, false otherwise.
   */
  public static function tableExists(): bool
  {
    $sql = "SHOW TABLES LIKE 'favorite'";
    $stmt = static::getDatabase()->query($sql);
    return $stmt && $stmt->fetchColumn();
  }

  /**
   * Retrieves all favorite vendors for a specific user.
   *
   * @param int $user_id The ID of the user.
   * @return array Array of Vendor objects, each with an optional profile image.
   */
  public static function fetchFavoritesForUser(int $user_id): array
  {
    $sql = "SELECT v.*, pi.file_path AS profile_image
            FROM favorite f
            JOIN vendor v ON f.vendor_id = v.vendor_id
            LEFT JOIN profile_image pi ON v.user_id = pi.user_id
            WHERE f.user_id = ?";

    $stmt = static::getDatabase()->prepare($sql);
    $stmt->execute([$user_id]);

    $raw_vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $vendors = [];
    foreach ($raw_vendors as $row) {
      $vendor = new Vendor($row);
      $vendor->profile_image = $row['profile_image'] ?? 'img/upload/users/default.png';
      $vendors[] = $vendor;
    }

    return $vendors;
  }

  /**
   * Toggles the favorite status for a vendor by a user.
   *
   * @param int $user_id The user ID.
   * @param int $vendor_id The vendor ID.
   * @return bool|null True if added, false if removed, null on failure.
   */
  public static function toggle($user_id, $vendor_id): ?bool
  {
    if (static::isFavorited($user_id, $vendor_id)) {
      return static::remove($user_id, $vendor_id) ? false : null;
    } else {
      return static::add($user_id, $vendor_id) ? true : null;
    }
  }

  /**
   * Checks if a vendor is favorited by a user.
   *
   * @param int $user_id The user ID.
   * @param int $vendor_id The vendor ID.
   * @return bool True if favorited, false otherwise.
   */
  public static function isFavorited(int $user_id, int $vendor_id): bool
  {
    $db = static::getDatabase();
    $sql = "SELECT COUNT(*) FROM favorite WHERE user_id = ? AND vendor_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$user_id, $vendor_id]);
    return $stmt->fetchColumn() > 0;
  }

  /**
   * Adds a vendor to a user's favorites.
   *
   * @param int $user_id The user ID.
   * @param int $vendor_id The vendor ID.
   * @return bool True on success, false on failure.
   */
  public static function add(int $user_id, int $vendor_id): bool
  {
    $db = static::getDatabase();
    $sql = "INSERT INTO favorite (user_id, vendor_id) VALUES (?, ?)";
    $stmt = $db->prepare($sql);
    return $stmt->execute([$user_id, $vendor_id]);
  }

  /**
   * Removes a vendor from a user's favorites.
   *
   * @param int $user_id The user ID.
   * @param int $vendor_id The vendor ID.
   * @return bool True on success, false on failure.
   */
  public static function remove(int $user_id, int $vendor_id): bool
  {
    $db = static::getDatabase();
    $sql = "DELETE FROM favorite WHERE user_id = ? AND vendor_id = ?";
    $stmt = $db->prepare($sql);
    return $stmt->execute([$user_id, $vendor_id]);
  }
}
