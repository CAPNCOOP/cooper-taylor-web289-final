<?php
require_once 'private/initialize.php';
Session::require_login();

if (!Session::is_vendor()) {
  exit("❌ Unauthorized access.");
}

$product_id = $_GET['id'] ?? null;
if (!$product_id || !is_numeric($product_id)) {
  exit("❌ Invalid product ID.");
}

$vendor = Vendor::find_by_user_id(Session::user_id());
if (!$vendor) {
  exit("❌ Vendor profile not found.");
}

$product = Product::find_by_id($product_id);
if (!$product || $product->vendor_id !== $vendor->vendor_id) {
  exit("❌ You do not have permission to delete this product.");
}

try {
  $product->delete();
  $session->message("✅ Product deleted successfully!");
  redirect_to("manage_products.php");
} catch (Exception $e) {
  exit("❌ Error deleting product: " . h($e->getMessage()));
}
