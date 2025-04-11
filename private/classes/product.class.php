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

  public function __construct($args = [])
  {
    $this->vendor_id = $args['vendor_id'] ?? 0;
    $this->name = $args['name'] ?? '';
    $this->price = $args['price'] ?? 0.00;
    $this->amount_id = $args['amount_id'] ?? 0;
    $this->category_id = $args['category_id'] ?? 0;
    $this->description = $args['description'] ?? '';
  }

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
}
