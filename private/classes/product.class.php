<?php

class Product extends DatabaseObject
{

  static protected $primary_key = 'product_id';

  static protected $table_name = 'product';
  static protected $db_columns = ['product_id', 'vendor_id', 'name', 'price', 'amount_id', 'category_id', 'description'];

  public $product_id;
  public $vendor_id;
  public $name;
  public $price;
  public $amount_id;
  public $category_id;
  public $description;

  public string $tag_name = ''; // or make it nullable: ?string $tag_name = null;

  /**
   * Product constructor.
   *
   * @param array $args Optional. Key-value pairs to initialize product properties.
   */
  public function __construct($args = [])
  {
    $this->vendor_id = $args['vendor_id'] ?? 0;
    $this->name = $args['name'] ?? '';
    $this->price = $args['price'] ?? 0.00;
    $this->amount_id = $args['amount_id'] ?? 0;
    $this->category_id = $args['category_id'] ?? 0;
    $this->description = $args['description'] ?? '';
  }

  /**
   * Deletes the product and all of its dependent records.
   *
   * @return bool True on success, false on failure.
   */
  public function delete(): bool
  {
    global $db;

    // Delete dependencies
    $stmt = $db->prepare("DELETE FROM product_image WHERE product_id = ?");
    $stmt->execute([$this->product_id]);

    $stmt = $db->prepare("DELETE FROM product_tag_map WHERE product_id = ?");
    $stmt->execute([$this->product_id]);
    $db->exec("SET innodb_lock_wait_timeout = 1");

    // Then call parent delete
    return parent::delete();
  }

  /**
   * Retrieves the file path of the product's primary image.
   *
   * @return string Image file path or a default placeholder if none found.
   */
  public function getImagePath(): string
  {
    $db = static::getDatabase();

    $sql = "SELECT file_path FROM product_image WHERE product_id = ? LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute([$this->product_id]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['file_path'] ?? 'default_product.png';
  }

  /**
   * Retrieves detailed product information by its ID, including
   * associated category, amount name, and product image path.
   *
   * @param int $id The ID of the product to retrieve.
   * @return array|null An associative array of product data, or null if not found.
   */
  public static function findById($id)
  {
    $sql = "SELECT 
    p.*, 
    c.category_name, 
    a.amount_name, 
    pi.file_path AS product_image
    FROM product p
    LEFT JOIN category c ON p.category_id = c.category_id
    LEFT JOIN amount_offered a ON p.amount_id = a.amount_id
    LEFT JOIN product_image pi ON p.product_id = pi.product_id
    WHERE p.product_id = ?";

    $stmt = self::$db->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * Retrieves an array of tag names associated with a specific product.
   *
   * @param int $product_id The ID of the product whose tags are being fetched.
   * @return array An array of tag name strings.
   */
  public static function fetchTags($product_id)
  {
    $sql = "SELECT t.tag_name
            FROM product_tag_map m
            JOIN product_tag t ON m.tag_id = t.tag_id
            WHERE m.product_id = ?";
    $stmt = self::$db->prepare($sql);
    $stmt->execute([$product_id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
  }
}
