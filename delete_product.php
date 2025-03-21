<?php
require_once 'private/initialize.php';

// Ensure the user is logged in and is a vendor
if (!isset($_SESSION['user_id']) || $_SESSION['user_level_id'] != 2) {
  die("❌ Unauthorized access.");
}

// Get the product ID from the request
$product_id = $_GET['id'] ?? null;
if (!$product_id) {
  die("❌ Invalid request.");
}

// Fetch the product to verify ownership
$sql = "SELECT vendor_id FROM product WHERE product_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
  die("❌ Product not found.");
}

$vendor_id = $product['vendor_id'];

// Ensure the logged-in vendor owns this product
// Fetch the logged-in vendor's vendor_id
$sql = "SELECT vendor_id FROM vendor WHERE user_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$vendor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vendor) {
  die("❌ Vendor profile not found.");
}

$logged_in_vendor_id = $vendor['vendor_id'];

// Ensure the logged-in vendor owns this product
if ($vendor_id != $logged_in_vendor_id) {
  die("❌ You do not have permission to delete this product.");
}

// Start transaction to delete from multiple tables safely
$db->beginTransaction();

try {
  // Delete product images
  $sql = "DELETE FROM product_image WHERE product_id = ?";
  $stmt = $db->prepare($sql);
  $stmt->execute([$product_id]);

  // Delete product tags
  $sql = "DELETE FROM product_tag_map WHERE product_id = ?";
  $stmt = $db->prepare($sql);
  $stmt->execute([$product_id]);

  // Finally, delete the product
  $sql = "DELETE FROM product WHERE product_id = ?";
  $stmt = $db->prepare($sql);
  $stmt->execute([$product_id]);

  // Commit the transaction
  $db->commit();

  // Redirect back with success message
  header("Location: manage_products.php?message=product_deleted");
  exit;
} catch (Exception $e) {
  $db->rollBack();
  die("❌ Error deleting product: " . $e->getMessage());
}
