<?php
$page_title = "Our Vendors";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_once 'private/functions.php';

// Pagination settings
$itemsPerPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

// Fetch all approved vendors with related products, markets, and locations
$sql = "SELECT v.vendor_id, v.business_name, v.vendor_bio, pi.file_path AS profile_image,
               GROUP_CONCAT(DISTINCT p.name SEPARATOR ', ') AS product_tags,
               GROUP_CONCAT(DISTINCT CONCAT(mw.week_start, ' - ', mw.week_end) SEPARATOR ', ') AS market_weeks,
               GROUP_CONCAT(DISTINCT s.state_abbr SEPARATOR ', ') AS state_abbrs,
               GROUP_CONCAT(DISTINCT s.state_name SEPARATOR ', ') AS state_names,
               GROUP_CONCAT(DISTINCT v.city SEPARATOR ', ') AS cities
        FROM vendor v
        LEFT JOIN profile_image pi ON v.user_id = pi.user_id
        LEFT JOIN product p ON v.vendor_id = p.vendor_id
        LEFT JOIN vendor_market vm ON v.vendor_id = vm.vendor_id
        LEFT JOIN market_week mw ON vm.week_id = mw.week_id
        LEFT JOIN state s ON v.state_id = s.state_id
        WHERE v.vendor_status = 'approved'";

if (!empty($searchTerm)) {
  $sql .= " HAVING LOWER(CONCAT_WS(' ', v.business_name, v.vendor_bio, product_tags, market_weeks, state_abbrs, state_names, cities)) LIKE ?";
}

$sql .= " GROUP BY v.vendor_id LIMIT ?, ?";

$stmt = $db->prepare($sql);

if (!empty($searchTerm)) {
  $searchTermWild = "%" . strtolower($searchTerm) . "%";
  $stmt->bindParam(1, $searchTermWild, PDO::PARAM_STR);
  $stmt->bindParam(2, $offset, PDO::PARAM_INT);
  $stmt->bindParam(3, $itemsPerPage, PDO::PARAM_INT);
} else {
  $stmt->bindParam(1, $offset, PDO::PARAM_INT);
  $stmt->bindParam(2, $itemsPerPage, PDO::PARAM_INT);
}

$stmt->execute();
$vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count total vendors for pagination
$sqlCount = "SELECT COUNT(DISTINCT v.vendor_id) AS total FROM vendor v WHERE v.vendor_status = 'approved'";

if (!empty($searchTerm)) {
  $sqlCount .= " AND LOWER(CONCAT_WS(' ', v.business_name, v.vendor_bio)) LIKE ?";
  $stmtCount = $db->prepare($sqlCount);
  $stmtCount->bindParam(1, $searchTermWild, PDO::PARAM_STR);
  $stmtCount->execute();
} else {
  $stmtCount = $db->query($sqlCount);
}

$totalVendors = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalVendors / $itemsPerPage);

// Search functionality
$searchTerm = $_GET['search'] ?? '';
$filteredVendors = [];

if (!empty($searchTerm)) {
  $searchTermLower = strtolower(trim($searchTerm)); // Fix strtolower usage

  foreach ($vendors as $vendor) {
    // Ensure all fields are concatenated properly for search
    $tags = strtolower(
      trim(
        ($vendor['product_tags'] ?? '') . ' ' .
          ($vendor['market_weeks'] ?? '') . ' ' .
          ($vendor['business_name'] ?? '') . ' ' .
          ($vendor['vendor_bio'] ?? '') . ' ' .
          ($vendor['state_abbrs'] ?? '') . ' ' .
          ($vendor['state_names'] ?? '') . ' ' .
          ($vendor['cities'] ?? '')
      )
    );

    // Check if the search term is contained within the tags
    if (strpos($tags, $searchTermLower) !== false) {
      $filteredVendors[] = $vendor;
    }
  }
} else {
  // If no search term, display all vendors
  $filteredVendors = $vendors;
}

?>

<div id="vendorhead">
  <h2>Our Vendors</h2>
  <form method="GET" action="ourvendors.php">
    <input type="text" id="searchBar" name="search" placeholder="Search vendors, products, locations..." value="<?= h($searchTerm) ?>">
    <button type="submit">Search</button>
  </form>
</div>

<div class="pagination">
  <?php if ($page > 1): ?>
    <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($searchTerm) ?>">&laquo; Prev</a>
  <?php endif; ?>

  <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?= $i ?>&search=<?= urlencode($searchTerm) ?>" class="<?= $i === $page ? 'active' : '' ?>">
      <?= $i ?>
    </a>
  <?php endfor; ?>

  <?php if ($page < $totalPages): ?>
    <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($searchTerm) ?>">Next &raquo;</a>
  <?php endif; ?>
</div>


<div id="vendor-list">
  <?php if (!empty($filteredVendors)): ?>
    <?php foreach ($filteredVendors as $vendor): ?>
      <a href="vendor_profile.php?vendor_id=<?= $vendor['vendor_id'] ?>">
        <div class="vendor-card"
          data-tags="<?php
                      $tags = array_filter([
                        $vendor['product_tags'] ?? '',
                        $vendor['market_weeks'] ?? '',
                        $vendor['state_abbrs'] ?? '',
                        $vendor['state_names'] ?? '',
                        $vendor['cities'] ?? ''
                      ]);
                      echo h(implode(', ', $tags));
                      ?>">
          <h2><?php echo h($vendor['business_name']); ?>, <?php echo nl2br(h($vendor['state_abbrs'])); ?></h2>
          <img src="<?php echo h($vendor['profile_image'] ?? 'default.png'); ?>" height="250" width="250" alt="Vendor Image">
          <p><?php echo nl2br(h($vendor['vendor_bio'])); ?></p>
        </div>
      </a>
    <?php endforeach; ?>
  <?php else: ?>
    <p>No results found for "<?php echo h($searchTerm); ?>"</p>
  <?php endif; ?>
</div>

<div class="pagination">
  <?php if ($page > 1): ?>
    <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($searchTerm) ?>">&laquo; Prev</a>
  <?php endif; ?>

  <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?= $i ?>&search=<?= urlencode($searchTerm) ?>" class="<?= $i === $page ? 'active' : '' ?>">
      <?= $i ?>
    </a>
  <?php endfor; ?>

  <?php if ($page < $totalPages): ?>
    <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($searchTerm) ?>">Next &raquo;</a>
  <?php endif; ?>
</div>

<?php require_once 'private/footer.php'; ?>
