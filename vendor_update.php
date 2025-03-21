<?php
$page_title = "Vendor - Update";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_once 'private/functions.php';

// Ensure user is logged in and is a vendor
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit("Redirecting to login...");
}

// Fetch vendor details
$user_id = $_SESSION['user_id'];
$sql = "SELECT v.vendor_id, v.business_name, v.contact_number, v.business_email, v.website, v.city, v.state_id, v.street_address, v.zip_code, pi.file_path AS profile_image
        FROM vendor v
        LEFT JOIN profile_image pi ON v.user_id = pi.user_id
        WHERE v.user_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id]);
$vendor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vendor) {
  header("Location: index.php");
  exit("Access denied: Vendor approval required.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $business_name = $_POST['business_name'] ?? '';
  $contact_number = $_POST['contact_number'] ?? '';
  $business_email = $_POST['business_email'] ?? '';
  $website = $_POST['website'] ?? '';
  $city = $_POST['city'] ?? '';
  $state_id = $_POST['state_id'] ?? '';
  $street_address = $_POST['street_address'] ?? '';
  $zip_code = $_POST['zip_code'] ?? '';

  $sql = "UPDATE vendor SET business_name = ?, contact_number = ?, business_email = ?, website = ?, city = ?, state_id = ?, street_address = ?, zip_code = ? WHERE user_id = ?";
  $stmt = $db->prepare($sql);
  $stmt->execute([$business_name, $contact_number, $business_email, $website, $city, $state_id, $street_address, $zip_code, $user_id]);

  header("Location: update_profile.php?success=1");
  exit;
}

?>

<body>
  <h1>Update Your Profile</h1>
  <?php if (isset($_GET['success'])): ?>
    <p>Profile updated successfully!</p>
  <?php endif; ?>
  <form method="post">
    <label>Business Name: <input type="text" name="business_name" value="<?php echo h($vendor['business_name']); ?>" required></label><br>

    <label>Contact Number: <input type="text" name="contact_number" value="<?php echo h($vendor['contact_number']); ?>"></label><br>

    <label>Business Email: <input type="email" name="business_email" value="<?php echo h($vendor['business_email']); ?>" required></label><br>

    <label>Website: <input type="text" name="website" value="<?php echo h($vendor['website']); ?>"></label><br>

    <label>City: <input type="text" name="city" value="<?php echo h($vendor['city']); ?>" required></label><br>

    <label>State ID: <input type="text" name="state_id" value="<?php echo h($vendor['state_id']); ?>" required></label><br>

    <label>Street Address: <input type="text" name="street_address" value="<?php echo h($vendor['street_address']); ?>"></label><br>

    <label>ZIP Code: <input type="text" name="zip_code" value="<?php echo h($vendor['zip_code']); ?>" required></label><br>

    <button type="submit">Update Profile</button>
  </form>

  <?php require_once 'private/footer.php'; ?>
