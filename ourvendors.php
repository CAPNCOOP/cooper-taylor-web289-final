<?php
require_once 'private/initialize.php';

// Pagination settings
$itemsPerPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

// Fetch all approved vendors with related products, markets, and locations
$sql = "SELECT v.vendor_id, v.business_name, v.vendor_bio, pi.file_path AS profile_image,
               GROUP_CONCAT(DISTINCT p.name SEPARATOR ', ') AS product_tags,
               GROUP_CONCAT(DISTINCT m.name SEPARATOR ', ') AS market_tags,
               GROUP_CONCAT(DISTINCT s.state_abbr SEPARATOR ', ') AS state_abbrs,
               GROUP_CONCAT(DISTINCT s.state_name SEPARATOR ', ') AS state_names,
               GROUP_CONCAT(DISTINCT v.city SEPARATOR ', ') AS cities
        FROM vendor v
        LEFT JOIN profile_image pi ON v.user_id = pi.user_id
        LEFT JOIN product p ON v.vendor_id = p.vendor_id
        LEFT JOIN vendor_market vm ON v.vendor_id = vm.vendor_id
        LEFT JOIN market m ON vm.market_id = m.market_id
        LEFT JOIN state s ON v.state_id = s.state_id
        WHERE v.vendor_status = 'approved'
        GROUP BY v.vendor_id
        LIMIT :offset, :itemsPerPage";
$stmt = $db->prepare($sql);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
$stmt->execute();
$vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count total vendors for pagination
$sqlCount = "SELECT COUNT(DISTINCT v.vendor_id) AS total FROM vendor v WHERE v.vendor_status = 'approved'";
$stmtCount = $db->query($sqlCount);
$totalVendors = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalVendors / $itemsPerPage);

$searchTerm = $_GET['search'] ?? '';
$filteredVendors = [];
if ($searchTerm) {
  $searchTermLower = strtolower($searchTerm);
  foreach ($vendors as $vendor) {
    $tags = strtolower($vendor['product_tags'] . ', ' . $vendor['market_tags'] . ', ' . $vendor['business_name'] . ', ' . $vendor['vendor_bio'] . ', ' . $vendor['state_abbrs'] . ', ' . $vendor['state_names'] . ', ' . $vendor['cities']);
    if (strpos($tags, $searchTermLower) !== false) {
      $filteredVendors[] = $vendor;
    }
  }
} else {
  $filteredVendors = $vendors;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Our Vendors</title>
  <link rel="stylesheet" href="css/styles.css">
</head>

<body class="ourvendors">
  <header>
    <h1>Blue Ridge Bounty</h1>
    <nav>
      <ul>
        <li><a href="index.php"><img src="img/assets/barn.png" alt="An icon of a barn" height="25" width="25"></a></li>
        <li><a href="schedule.php">Schedule</a></li>
        <li><a href="ourvendors.php">Our Vendors</a></li>
        <li><a href="aboutus.php">About Us</a></li>
        <li><a href="login.php"><img src="img/assets/user.png" alt="A user login icon." height="25" width="25"></a></li>
      </ul>
    </nav>
  </header>

  <div id="vendorhead">
    <h2>Our Vendors</h2>
    <form method="GET" action="ourvendors.php">
      <input type="text" id="searchBar" name="search" placeholder="Search vendors, products, markets, locations...">
      <button type="submit">Search</button>
    </form>
  </div>

  <div id="vendor-list">
    <?php if (!empty($filteredVendors)): ?>
      <?php foreach ($filteredVendors as $vendor): ?>
        <div class="vendor-card" data-tags="<?php echo htmlspecialchars($vendor['product_tags'] . ', ' . $vendor['market_tags'] . ', ' . $vendor['state_abbrs'] . ', ' . $vendor['state_names'] . ', ' . $vendor['cities']); ?>" onclick="window.location.href='vendor_profile.php?id=<?php echo $vendor['vendor_id']; ?>'">
          <h2><?php echo htmlspecialchars($vendor['business_name']); ?></h2>
          <img src="img/upload/users/<?php echo htmlspecialchars($vendor['profile_image'] ?? 'default.png'); ?>" height="250" width="250" alt="Vendor Image">
          <p><?php echo nl2br(htmlspecialchars($vendor['vendor_bio'])); ?></p>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No results found for "<?php echo htmlspecialchars($searchTerm); ?>"</p>
    <?php endif; ?>
  </div>

  <div id="pagination">
    <?php if ($totalPages > 1): ?>
      <p>Page <?php echo $page; ?> of <?php echo $totalPages; ?></p>
      <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>">Previous</a>
      <?php endif; ?>
      <?php if ($page < $totalPages): ?>
        <a href="?page=<?php echo $page + 1; ?>">Next</a>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</body>

</html>
