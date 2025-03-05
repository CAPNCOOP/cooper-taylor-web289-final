<?php
$page_title = "Vendor Dashboard";
require_once 'private/initialize.php';
require_once 'private/header.php';

if (!isset($db)) {
  exit("Database connection error.");
}

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit("Redirecting to login...");
}

// Fetch user and vendor details
$user_id = $_SESSION['user_id'];

$sql = "SELECT v.vendor_id, v.business_name, v.vendor_status, u.first_name, u.last_name, p.file_path AS profile_image
        FROM vendor v
        JOIN users u ON v.user_id = u.user_id
        LEFT JOIN profile_image p ON u.user_id = p.user_id
        WHERE v.user_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id]);
$vendor = $stmt->fetch(PDO::FETCH_ASSOC) ?: exit("Error: Vendor not found.");

// Redirect if not a vendor or not approved
if (!$vendor || $vendor['vendor_status'] !== 'approved') {
  header("Location: index.php");
  exit("Access denied: Vendor approval required.");
}

// Set profile image (default if not found)
$profile_image = !empty($vendor['profile_image']) ? $vendor['profile_image'] : "img/upload/users/default.png";
?>

<div id="vendor-info">
  <div>
    <h2><?php echo htmlspecialchars($vendor['first_name'] . ' ' . $vendor['last_name']); ?></h2>
    <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Vendor Profile Picture" height="250" width="250">
    <span>Business: <?php echo htmlspecialchars($vendor['business_name']); ?></span>
    <a href="edit_profile.php" class="btn"><img src="img/assets/edit.png" alt="An edit icon." height="50" width="50"></a>
  </div>

  <div>
    <h2>Dashboard Overview</h2>
    <p>Manage your products, RSVP for upcoming markets, and update your business profile.</p>
    <nav>
      <ul>
        <li><a href="manage_products.php">Manage Products</a></li>
        <li><a href="rsvp_market.php">RSVP for Markets</a></li>
      </ul>
    </nav>
  </div>
</div>

<?php require_once 'private/footer.php'; ?>
