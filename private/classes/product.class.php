<?php

class Product extends DatabaseObject
{
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
}
