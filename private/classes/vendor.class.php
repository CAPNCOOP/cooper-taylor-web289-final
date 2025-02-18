<?php

class Vendor extends User
{
  static protected $table_name = 'vendor';
  static protected $db_columns = ['vendor_id', 'user_id', 'business_name', 'contact_number', 'business_EIN', 'business_email', 'website', 'city', 'state_id', 'street_address', 'zip_code', 'description', 'vendor_bio'];

  public $vendor_id;
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

  public function __construct($args = [])
  {
    parent::__construct($args);
    $this->vendor_id = $args['vendor_id'] ?? 0;
    $this->business_name = $args['business_name'] ?? '';
    $this->contact_number = $args['contact_number'] ?? '';
    $this->business_EIN = $args['business_EIN'] ?? '';
    $this->business_email = $args['business_email'] ?? '';
    $this->website = $args['website'] ?? '';
    $this->city = $args['city'] ?? '';
    $this->state_id = $args['state_id'] ?? 0;
    $this->street_address = $args['street_address'] ?? '';
    $this->zip_code = $args['zip_code'] ?? '';
    $this->description = $args['description'] ?? '';
    $this->vendor_bio = $args['vendor_bio'] ?? '';
  }
}
