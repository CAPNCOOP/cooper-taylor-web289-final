<?php
class Admin extends User
{
  static protected $table_name = 'users';
  static protected $db_columns = ['user_id', 'username', 'password', 'email', 'first_name', 'last_name', 'user_level_id', 'is_active'];

  /**
   * Retrieves select U.S. state IDs and abbreviations.
   *
   * @return array List of states with state_id and state_abbr.
   */
  public static function fetchStates(): array
  {
    $db = static::getDatabase();
    $stmt = $db->query("SELECT state_id, state_abbr FROM state ORDER BY state_abbr ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Toggles a user's active status.
   *
   * @param int $user_id The ID of the user to update.
   * @return bool True on success, false on failure.
   */
  public function toggleUserStatus($user_id): bool
  {
    $user = self::find_by_id($user_id);
    if ($user) {
      $user->is_active = ($user->is_active == 1) ? 0 : 1;
      return $user->save();
    }
    return false;
  }

  /**
   * Toggles a vendor's user account active status.
   *
   * @param int $userId The vendor's ID.
   * @return bool True on success, false on failure.
   */
  public function toggleVendorStatus($userId): bool
  {
    $vendor = Vendor::find_by_id(id: $userId);
    if ($vendor) {
      // Get the associated user record
      $user = User::find_by_id(id: $vendor->user_id);
      if ($user) {
        // Toggle the is_active status on the user record
        $user->is_active = ($user->is_active == 1) ? 0 : 1;
        return $user->save();
      }
    }
    return false;
  }

  /**
   * Retrieves all users except super admins.
   *
   * @return array List of User objects.
   */
  public function fetchUsers()
  {
    return User::find_by_sql("SELECT * FROM users WHERE user_level_id = 1");
  }

  /**
   * Retrieves all vendors with associated user info.
   *
   * @return array List of Vendor objects with user details.
   */
  public function fetchVendors()
  {
    $sql = "SELECT v.*, u.first_name, u.last_name, u.email, u.is_active
    FROM vendor v
    JOIN users u ON v.user_id = u.user_id
    WHERE u.user_level_id = 2";

    return Vendor::find_by_sql($sql);
  }

  /**
   * Retrieves vendors with 'pending' or 'denied' status.
   *
   * @return array Array of vendor_id, business_name, vendor_status, and email.
   */
  public function fetchPendingVendors(): array
  {
    $sql = "SELECT v.vendor_id, v.business_name, v.vendor_status, u.email 
    FROM vendor v 
    JOIN users u ON v.user_id = u.user_id 
    WHERE v.vendor_status IN ('pending', 'denied')";

    $stmt = self::$db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // or hydrate into Vendor objects if you want
  }

  /**
   * Retrieves RSVP status for upcoming markets, optionally for a single vendor.
   *
   * @param int $vendor_id Optional. If provided, returns RSVP data for that vendor only.
   * @return array A vendor-to-week map of RSVP statuses.
   */
  public static function fetchVendorRsvps(int $vendor_id = 0): array
  {
    $sql = "
      SELECT vm.vendor_id, mw.week_id, mw.week_start, mw.week_end, mw.confirmation_deadline,
             vm.status AS rsvp_status
      FROM vendor_market vm
      JOIN market_week mw ON vm.week_id = mw.week_id
      WHERE mw.week_start >= CURDATE()
        AND mw.is_deleted = 0
    ";

    if ($vendor_id !== 0) {
      $sql .= " AND vm.vendor_id = :vendor_id";
    }

    $sql .= " ORDER BY mw.week_start ASC";

    $stmt = self::$db->prepare($sql);

    if ($vendor_id !== 0) {
      $stmt->bindValue(':vendor_id', $vendor_id, PDO::PARAM_INT);
    }

    $stmt->execute();
    $rsvps = $stmt->fetchAll();
    $map = [];

    foreach ($rsvps as $rsvp) {
      if ($vendor_id !== 0) {
        $map[$rsvp['week_id']] = $rsvp['rsvp_status'];
      } else {
        $map[$rsvp['vendor_id']][$rsvp['week_id']] = $rsvp['rsvp_status'];
      }
    }

    return $map;
  }

  /**
   * Updates RSVP status for a vendor for a specific week.
   *
   * @param int $vendorId The vendor's ID.
   * @param int $weekId The week ID.
   * @param string $status The new RSVP status.
   * @return bool True on success, false on failure.
   */
  public function updateVendorRsvpStatus(int $vendorId, int $weekId, string $status): bool
  {
    $sql = "UPDATE vendor_market SET status = ? WHERE vendor_id = ? AND week_id = ?";
    $stmt = self::$db->prepare($sql);
    return $stmt->execute([$status, $vendorId, $weekId]);
  }

  /**
   * Inserts or updates a vendor's RSVP for a specific week.
   *
   * @param int $vendor_id The vendor's ID.
   * @param int $week_id The market week ID.
   * @param string $status The RSVP status to save.
   * @return void
   */
  public static function saveVendorRsvp(int $vendor_id, int $week_id, string $status): void
  {
    $db = static::getDatabase();

    $sql = "SELECT 1 FROM vendor_market WHERE vendor_id = ? AND week_id = ? LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute([$vendor_id, $week_id]);
    $exists = $stmt->fetchColumn();

    if ($exists) {
      $sql = "UPDATE vendor_market SET status = ? WHERE vendor_id = ? AND week_id = ?";
      $stmt = $db->prepare($sql);
      $stmt->execute([$status, $vendor_id, $week_id]);
    } else {
      $sql = "INSERT INTO vendor_market (vendor_id, week_id, status) VALUES (?, ?, ?)";
      $stmt = $db->prepare($sql);
      $stmt->execute([$vendor_id, $week_id, $status]);
    }
  }

  /**
   * Retrieves confirmed market weeks grouped by vendor.
   *
   * @return array Map of vendor_id to their confirmed weeks.
   */
  public function fetchMarketWeeksByVendor()
  {
    $sql = "SELECT vm.vendor_id, mw.week_id, mw.week_start, mw.week_end
            FROM vendor_market vm
            JOIN market_week mw ON vm.week_id = mw.week_id
            WHERE vm.status = 'confirmed' AND mw.is_deleted = 0
            ORDER BY mw.week_start ASC";
    $stmt = self::$db->prepare($sql);
    $stmt->execute();

    $weeks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $map = [];
    foreach ($weeks as $week) {
      $map[$week['vendor_id']][] = [
        'week_id' => $week['week_id'],
        'week_start' => $week['week_start'],
        'week_end' => $week['week_end']
      ];
    }
    return $map;
  }

  /**
   * Retrieves all upcoming market weeks.
   *
   * @return array List of market weeks including status and deadlines.
   */
  public static function fetchUpcomingMarkets()
  {
    $sql = "
    SELECT week_id, week_start, week_end, market_status, confirmation_deadline
    FROM market_week
    WHERE week_start >= CURDATE() AND is_deleted = 0
    ORDER BY week_start ASC
  ";

    $stmt = self::$db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Retrieves the next upcoming market week's end date and status.
   *
   * @return array|null Associative array with week_end and market_status, or null if none found.
   */
  public function fetchUpcomingMarketWeek()
  {
    $stmt = self::$db->prepare("SELECT week_end, market_status FROM market_week WHERE week_end >= CURDATE() ORDER BY week_end ASC LIMIT 1");
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * Retrieves start and end dates for a market week.
   *
   * @param int $week_id The market week's ID.
   * @return array|null Associative array with week_start and week_end, or null if not found.
   */
  public function fetchMarketById(int $week_id): ?array
  {
    $sql = "SELECT week_start, week_end FROM market_week WHERE week_id = ?";
    $stmt = self::$db->prepare($sql);
    $stmt->execute([$week_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
  }

  /**
   * Retrieves confirmed vendors attending a specific market week.
   *
   * @param int $week_id The market week ID.
   * @return array List of vendor info with profile photo.
   */
  public function fetchVendorsForMarketWeek(int $week_id): array
  {
    $sql = "
    SELECT v.vendor_id, v.user_id, v.business_name, v.vendor_bio, v.city, s.state_abbr, 
           pi.file_path AS profile_photo
    FROM vendor_market vm
    JOIN vendor v ON vm.vendor_id = v.vendor_id
    JOIN state s ON v.state_id = s.state_id
    LEFT JOIN profile_image pi ON v.user_id = pi.user_id
    WHERE vm.week_id = ? AND vm.status = 'confirmed'
  ";

    $stmt = self::$db->prepare($sql);
    $stmt->execute([$week_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Creates a new market week with validation.
   *
   * @param string $week_start The start date.
   * @param string $week_end The end date.
   * @param string $confirmation_deadline Deadline for vendors to confirm.
   * @return string Status message indicating success or error.
   */
  public function createMarketWeek(string $week_start, string $week_end, string $confirmation_deadline): string
  {
    // Validate date logic
    if ($week_start >= $week_end) {
      return "❌ Week start must be before week end.";
    }

    if ($confirmation_deadline >= $week_start) {
      return "❌ Deadline must be before week start.";
    }

    // Check if the week_start already exists
    $check = self::$db->prepare("SELECT COUNT(*) FROM market_week WHERE week_start = ?");
    $check->execute([$week_start]);

    if ($check->fetchColumn() > 0) {
      return "❌ A market is already scheduled for this date.";
    }

    // Insert new market week
    $sql = "INSERT INTO market_week (week_start, week_end, market_status, confirmation_deadline)
            VALUES (?, ?, 'confirmed', ?)";
    $stmt = self::$db->prepare($sql);
    $success = $stmt->execute([$week_start, $week_end, $confirmation_deadline]);

    return $success ? "✅ Market week scheduled!" : "❌ Failed to schedule market.";
  }

  /**
   * Cancels a market week by setting status to 'cancelled'.
   *
   * @param int $weekId The week ID.
   * @return bool True on success, false on failure.
   */
  public function cancelMarketWeek($weekId): bool
  {
    $stmt = self::$db->prepare("UPDATE market_week SET market_status = 'cancelled' WHERE week_id = ?");
    return $stmt->execute([$weekId]);
  }

  /**
   * Soft deletes a market week by setting is_deleted to 1.
   *
   * @param int $weekId The week ID.
   * @return bool True on success, false on failure.
   */
  public function deleteMarketWeek(int $weekId): bool
  {
    $sql = "UPDATE market_week SET is_deleted = 1 WHERE week_id = ?";
    $stmt = self::$db->prepare($sql);
    return $stmt->execute([$weekId]);
  }

  /**
   * Updates the status of a market week.
   *
   * @param int $week_id The week ID.
   * @param string $status The new market status.
   * @return void
   */
  public static function toggleMarketStatus(int $week_id, string $status): void
  {
    $db = static::getDatabase();
    $stmt = $db->prepare("UPDATE market_week SET market_status = ? WHERE week_id = ?");
    $stmt->execute([$status, $week_id]);
  }

  /**
   * Retrieves the welcome section content from the homepage.
   *
   * @return string|null The content, or null if not found.
   */
  public function fetchHomepageContent()
  {
    $sql = "SELECT content FROM homepage_content WHERE section = 'welcome' LIMIT 1";
    $stmt = self::$db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchColumn();
  }

  /**
   * Updates or inserts content for a specific homepage section.
   *
   * @param string $section The section name.
   * @param string $content The new content.
   * @return bool True on success, false on failure.
   */
  public function updateHomepageContent(string $section, string $content): bool
  {
    // Check if section exists
    $stmt = self::$db->prepare("SELECT COUNT(*) FROM homepage_content WHERE section = ?");
    $stmt->execute([$section]);

    if ($stmt->fetchColumn() > 0) {
      $update = self::$db->prepare("UPDATE homepage_content SET content = ? WHERE section = ?");
      return $update->execute([$content, $section]);
    } else {
      $insert = self::$db->prepare("INSERT INTO homepage_content (section, content) VALUES (?, ?)");
      return $insert->execute([$section, $content]);
    }
  }
}
