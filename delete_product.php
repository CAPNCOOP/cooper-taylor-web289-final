<?php
require_once 'private/initialize.php';
require_login();

if (!isset($_SESSION['user_id']) || $_SESSION['user_level_id'] != 2) {
  die("❌ Unauthorized access.");
}

$product_id = $_GET['id'] ?? null;
if (!$product_id) {
  die("❌ Invalid request.");
}

$vendor = Vendor::find_by_user_id($_SESSION['user_id']);
if (!$vendor) {
  die("❌ Vendor profile not found.");
}

$product = Product::find_by_id($product_id);
if (!$product || $product->vendor_id != $vendor->vendor_id) {
  die("❌ You do not have permission to delete this product.");
}

$db->beginTransaction();

try {
  $stmt = $db->prepare("DELETE FROM product_image WHERE product_id = ?");
  $stmt->execute([$product_id]);

  $stmt = $db->prepare("DELETE FROM product_tag_map WHERE product_id = ?");
  $stmt->execute([$product_id]);

  $product->delete();

  $db->commit();
  redirect_to("manage_products.php?message=product_deleted");
} catch (Exception $e) {
  $db->rollBack();
  die("❌ Error deleting product: " . $e->getMessage());
}
