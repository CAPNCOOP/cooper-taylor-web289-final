<?php
require_once 'private/initialize.php';
require_login();

if (!Session::is_vendor()) {
  die("❌ Unauthorized access.");
}

$product_id = $_GET['id'] ?? null;
if (!$product_id) {
  die("❌ Invalid request.");
}

$vendor = Vendor::find_by_user_id(Session::user_id());
if (!$vendor) {
  die("❌ Vendor profile not found.");
}

$product = Product::find_by_id($product_id);
if (!$product || $product->vendor_id != $vendor->vendor_id) {
  die("❌ You do not have permission to delete this product.");
}

try {
  $product->delete();

  redirect_to("manage_products.php?message=product_deleted");
} catch (Exception $e) {
  $db->rollBack();
  die("❌ Error deleting product: " . $e->getMessage());
}
