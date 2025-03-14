<?php
$page_title = "Vendor - Update";
require_once 'private/initialize.php';
require_once 'private/header.php';

require_login(); // Ensure the user is logged in

// Fetch vendor details
$user_id = $_SESSION['user_id'];
$sql = "SELECT v.vendor_id, v.business_name, v.contact_number, v.business_email, v.website, 
               v.city, v.state_id, v.street_address, v.zip_code, pi.file_path AS profile_image
        FROM vendor v
        LEFT JOIN profile_image pi ON v.user_id = pi.user_id
        WHERE v.user_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id]);
$vendor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vendor) {
  $_SESSION['message'] = "❌ Vendor not found.";
  header("Location: index.php");
  exit;
}

// ✅ Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // CSRF Protection
  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['message'] = "❌ CSRF token mismatch. Try again.";
    header("Location: vendor_update.php");
    exit;
  }

  // ✅ Sanitize & Validate Input
  $business_name = trim($_POST['business_name'] ?? '');
  $contact_number = trim($_POST['contact_number'] ?? '');
  $business_email = filter_var($_POST['business_email'] ?? '', FILTER_VALIDATE_EMAIL);
  $website = filter_var($_POST['website'] ?? '', FILTER_SANITIZE_URL);
  $city = trim($_POST['city'] ?? '');
  $state_id = filter_var($_POST['state_id'] ?? '', FILTER_VALIDATE_INT);
  $street_address = trim($_POST['street_address'] ?? '');
  $zip_code = trim($_POST['zip_code'] ?? '');

  if (!$business_name || !$business_email || !$city || !$state_id || !$zip_code) {
    $_SESSION['message'] = "❌ All required fields must be filled correctly.";
    header("Location: vendor_update.php");
    exit;
  }

  // ✅ Ensure Vendor Exists
  $stmt = $db->prepare("SELECT vendor_id FROM vendor WHERE user_id = ?");
  $stmt->execute([$user_id]);
  if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
    $_SESSION['message'] = "❌ Vendor not found.";
    header("Location: index.php");
    exit;
  }

  // ✅ Update Vendor Details
  $sql = "UPDATE vendor SET business_name = ?, contact_number = ?, business_email = ?, website = ?, 
                city = ?, state_id = ?, street_address = ?, zip_code = ? WHERE user_id = ?";
  $stmt = $db->prepare($sql);
  $stmt->execute([
    $business_name,
    $contact_number,
    $business_email,
    $website,
    $city,
    $state_id,
    $street_address,
    $zip_code,
    $user_id
  ]);

  $_SESSION['message'] = "✅ Vendor profile updated successfully!";
  header("Location: vendor_update.php");
  exit;
}

require_once 'private/popup_message.php';
?>

<body>
  <h1>Update Your Vendor Profile</h1>

  <form method="post">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

    <label for="business_name">Business Name:</label>
    <input type="text" id="business_name" name="business_name" value="<?= htmlspecialchars($vendor['business_name']); ?>" required>

    <label for="contact_number">Contact Number:</label>
    <input type="text" id="contact_number" name="contact_number" value="<?= htmlspecialchars($vendor['contact_number']); ?>">

    <label for="business_email">Business Email:</label>
    <input type="email" id="business_email" name="business_email" value="<?= htmlspecialchars($vendor['business_email']); ?>" required>

    <label for="website">Website:</label>
    <input type="url" id="website" name="website" value="<?= htmlspecialchars($vendor['website']); ?>">

    <label for="city">City:</label>
    <input type="text" id="city" name="city" value="<?= htmlspecialchars($vendor['city']); ?>" required>

    <label for="state_id">State:</label>
    <input type="text" id="state_id" name="state_id" value="<?= htmlspecialchars($vendor['state_id']); ?>" required>

    <label for="zip_code">ZIP Code:</label>
    <input type="text" id="zip_code" name="zip_code" value="<?= htmlspecialchars($vendor['zip_code']); ?>" required>

    <button type="submit">Update Profile</button>
  </form>

  <?php require_once 'private/footer.php'; ?>
