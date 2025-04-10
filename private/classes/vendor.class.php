<?php

class Vendor extends User
{
  protected static $primary_key = 'vendor_id';
  static protected $table_name = 'vendor';
  static protected $db_columns = ['vendor_id', 'user_id', 'business_name', 'contact_number', 'business_EIN', 'business_email', 'website', 'city', 'state_id', 'street_address', 'zip_code', 'description', 'vendor_bio', 'vendor_status'];

  public $vendor_id;
  public $user_id;
  public $business_name;
  public $contact_number;
  public $business_EIN;
  public $business_email;
  public $website;
  public $city;
  public $state_id;
  public $street_address;
  public $zip_code;
  public $description;
  public $vendor_bio;

  public $vendor_status = 'pending'; // default status


  // Not part of vendor.class.php, used by fetchApprovedVendorsWithTags
  public $profile_image;
  public $product_tags;
  public $market_weeks;
  public $state_abbrs;
  public $state_names;
  public $cities;


  public function __construct($args = [])
  {
    parent::__construct($args);

    foreach ($args as $key => $value) {
      if (property_exists($this, $key)) {
        $this->$key = $value;
      }
    }
  }


  public function approve(): bool
  {
    $this->vendor_status = 'approved';
    return $this->save();
  }

  public function reject(): bool
  {
    $this->vendor_status = 'denied';
    return $this->save();
  }

  public static function fetchProducts($vendor_id): array
  {
    $sql = "SELECT p.*, a.amount_name, pi.file_path AS product_image
            FROM product p
            LEFT JOIN amount_offered a ON p.amount_id = a.amount_id
            LEFT JOIN product_image pi ON p.product_id = pi.product_id
            WHERE p.vendor_id = ?";

    $stmt = self::$db->prepare($sql);
    $stmt->execute([$vendor_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function fetchUpcomingMarkets(int $vendor_id): array
  {
    $sql = "SELECT mw.week_start, mw.week_end, mw.market_status
            FROM vendor_market vm
            LEFT JOIN market_week mw ON vm.week_id = mw.week_id
            WHERE vm.vendor_id = ? AND mw.week_start >= CURDATE()
            ORDER BY mw.week_start ASC
            LIMIT 5";

    $stmt = self::$db->prepare($sql);
    $stmt->execute([$vendor_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function fetchApprovedVendorsWithTags(int $offset, int $limit, string $search = ''): array
  {
    $db = static::getDatabase();

    $sql = "SELECT v.vendor_id, v.business_name, v.vendor_bio, 
                   pi.file_path AS profile_image,
                   GROUP_CONCAT(DISTINCT p.name SEPARATOR ', ') AS product_tags,
                   GROUP_CONCAT(DISTINCT CONCAT(mw.week_start, ' - ', mw.week_end) SEPARATOR ', ') AS market_weeks,
                   GROUP_CONCAT(DISTINCT s.state_abbr SEPARATOR ', ') AS state_abbrs,
                   GROUP_CONCAT(DISTINCT s.state_name SEPARATOR ', ') AS state_names,
                   GROUP_CONCAT(DISTINCT v.city SEPARATOR ', ') AS cities
            FROM vendor v
            LEFT JOIN profile_image pi ON v.user_id = pi.user_id
            LEFT JOIN product p ON v.vendor_id = p.vendor_id
            LEFT JOIN vendor_market vm ON v.vendor_id = vm.vendor_id
            LEFT JOIN market_week mw ON vm.week_id = mw.week_id
            LEFT JOIN state s ON v.state_id = s.state_id
            WHERE v.vendor_status = 'approved'";

    if (!empty($search)) {
      $sql .= " HAVING LOWER(CONCAT_WS(' ', v.business_name, v.vendor_bio, product_tags, market_weeks, state_abbrs, state_names, cities)) LIKE :searchTerm";
    }

    $sql .= " GROUP BY v.vendor_id LIMIT :offset, :limit";

    $stmt = $db->prepare($sql);

    if (!empty($search)) {
      $stmt->bindValue(':searchTerm', '%' . strtolower($search) . '%', PDO::PARAM_STR);
    }

    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_CLASS, static::class);
  }

  public static function countApprovedVendors(string $search = ''): int
  {
    $db = static::getDatabase();

    $sql = "SELECT COUNT(DISTINCT v.vendor_id) AS total FROM vendor v WHERE v.vendor_status = 'approved'";

    if (!empty($search)) {
      $sql .= " AND LOWER(CONCAT_WS(' ', v.business_name, v.vendor_bio)) LIKE :searchTerm";
      $stmt = $db->prepare($sql);
      $stmt->bindValue(':searchTerm', '%' . strtolower($search) . '%', PDO::PARAM_STR);
      $stmt->execute();
    } else {
      $stmt = $db->query($sql);
    }

    return (int)$stmt->fetchColumn();
  }

  public static function find_by_user_id(int $user_id): ?self
  {
    $sql = "SELECT vendor_id, user_id, business_name, contact_number, business_EIN, business_email, website,
    city, state_id, street_address, zip_code, description, vendor_bio, vendor_status
    FROM vendor
    WHERE user_id = :user_id
    LIMIT 1";

    $stmt = static::getDatabase()->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_CLASS, static::class);
    $vendor = $stmt->fetch();

    return $vendor ?: null;
  }
}
