<?php
class Admin extends User
{
  static protected $table_name = 'users';
  static protected $db_columns = ['user_id', 'username', 'password', 'email', 'first_name', 'last_name', 'user_level_id', 'is_active'];

  public function toggleUserStatus($userId): bool
  {
    $user = self::find_by_id($userId);
    if ($user) {
      $user->is_active = !$user->is_active;
      return $user->save();
    }
    return false;
  }

  public function toggleVendorStatus($userId): bool
  {
    $vendor = Vendor::find_by_id($userId);
    if ($vendor) {
      $vendor->is_active = !$vendor->is_active;
      return $vendor->save();
    }
    return false;
  }


  // Override vendor RSVP to 'confirmed'
  public function overrideVendorSchedule($vendorId, $marketDate)
  {
    $sql = "UPDATE vendor_market SET status = 'confirmed' WHERE vendor_id = ? AND attend_date = ?";
    $stmt = self::$db->prepare($sql);
    return $stmt->execute([$vendorId, $marketDate]);
  }

  // Fetch all non-superadmin users
  public function fetchUsers()
  {
    return User::find_by_sql("SELECT * FROM users WHERE user_level_id != 4");
  }

  // Fetch all vendors and their related user info
  public function fetchVendors()
  {
    return Vendor::find_by_sql(
      "SELECT v.*, u.first_name, u.last_name, u.email, u.is_active
       FROM vendor v
       JOIN users u ON v.user_id = u.user_id
       WHERE u.user_level_id = 2"
    );
  }

  // fetch pending vendors
  public function fetchPendingVendors(): array
  {
    $sql = "SELECT v.vendor_id, v.business_name, u.email 
              FROM vendor v 
              JOIN users u ON v.user_id = u.user_id 
              WHERE v.vendor_status IN ('pending', 'denied')";

    $stmt = self::$db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // or hydrate into Vendor objects if you want
  }


  // Fetch RSVP statuses per vendor per week
  // Inside Admin or SuperAdmin class (which extends DatabaseObject)
  public static function fetchVendorRsvps(int $vendor_id): array
  {
    $sql = "
    SELECT vm.vendor_id, mw.week_id, mw.week_start, mw.week_end, mw.confirmation_deadline, vm.status AS rsvp_status
    FROM vendor_market vm
    JOIN market_week mw ON vm.week_id = mw.week_id
    WHERE mw.week_start >= CURDATE() AND mw.is_deleted = 0
    ORDER BY mw.week_start ASC
  ";

    $stmt = self::$db->prepare($sql);
    $stmt->execute();

    $rsvps = $stmt->fetchAll();
    $map = [];

    foreach ($rsvps as $rsvp) {
      $map[$rsvp['vendor_id']][$rsvp['week_id']] = $rsvp['rsvp_status'];
    }

    return $map;
  }

  public function updateVendorRsvpStatus(int $vendorId, int $weekId, string $status): bool
  {
    $sql = "UPDATE vendor_market SET status = ? WHERE vendor_id = ? AND week_id = ?";
    $stmt = self::$db->prepare($sql);
    return $stmt->execute([$status, $vendorId, $weekId]);
  }

  // Fetch confirmed market weeks by vendor
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

  // Fetch all upcoming market weeks
  public static function fetchUpcomingMarkets()
  {
    $sql = "
    SELECT week_id, week_start, week_end, confirmation_deadline
    FROM market_week
    WHERE week_start >= CURDATE() AND is_deleted = 0
    ORDER BY week_start ASC
  ";

    $stmt = self::$db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function fetchMarketById(int $week_id): ?array
  {
    $sql = "SELECT week_start, week_end FROM market_week WHERE week_id = ?";
    $stmt = self::$db->prepare($sql);
    $stmt->execute([$week_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
  }

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


  public function cancelMarketWeek($weekId): bool
  {
    $stmt = self::$db->prepare("UPDATE market_week SET market_status = 'cancelled' WHERE week_id = ?");
    return $stmt->execute([$weekId]);
  }

  public function deleteMarketWeek(int $weekId): bool
  {
    $sql = "UPDATE market_week SET is_deleted = 1 WHERE week_id = ?";
    $stmt = self::$db->prepare($sql);
    return $stmt->execute([$weekId]);
  }



  // Get homepage welcome message
  public function fetchHomepageContent()
  {
    $sql = "SELECT content FROM homepage_content WHERE section = 'welcome' LIMIT 1";
    $stmt = self::$db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchColumn();
  }

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
