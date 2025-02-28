<?php
require_once 'private/initialize.php';
require_once 'private/header.php';
$page_title = "Vendor - Dashboard"; // Set dynamic title
if (!isset($db)) {
  exit("Database connection error.");
}

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit("Redirecting to login...");
}

// Fetch user and vendor details
$user_id = $_SESSION['user_id'];
$sql = "SELECT v.vendor_id, v.business_name, v.vendor_status, u.first_name, u.last_name
        FROM vendor v
        JOIN users u ON v.user_id = u.user_id
        WHERE v.user_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id]);
$vendor = $stmt->fetch(PDO::FETCH_ASSOC) ?: exit("Error: Vendor not found.");

// Redirect if not a vendor or not approved
if (!$vendor || $vendor['vendor_status'] !== 'approved') {
  header("Location: index.php");
  exit("Access denied: Vendor approval required.");
}

?>

<body>
  <h1>Welcome, <?php echo htmlspecialchars($vendor['first_name'] . ' ' . $vendor['last_name']); ?></h1>
  <h2>Business: <?php echo htmlspecialchars($vendor['business_name']); ?></h2>

  <nav>
    <ul>
      <li><a href="manage_products.php">Manage Products</a></li>
      <li><a href="rsvp_market.php">RSVP for Markets</a></li>
      <li><a href="update_profile.php">Update Profile</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </nav>

  <h2>Dashboard Overview</h2>
  <p>Manage your products, RSVP for upcoming markets, and update your business profile.</p>
</body>

</html>
