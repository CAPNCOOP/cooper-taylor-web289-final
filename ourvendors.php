<?php
$page_title = "Our Vendors";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_once 'private/functions.php';

// Pagination setup
$itemsPerPage = 12;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;
$searchTerm = $_GET['search'] ?? '';

// Fetch vendors using OOP
$vendors = Vendor::fetchApprovedVendorsWithTags($offset, $itemsPerPage, $searchTerm);
$totalVendors = Vendor::countApprovedVendors($searchTerm);
$totalPages = ceil($totalVendors / $itemsPerPage);

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
  <?php if (!empty($vendors)): ?>
    <?php foreach ($vendors as $vendor): ?>
      <a href="vendor_profile.php?vendor_id=<?= h($vendor->vendor_id) ?>">
        <div class="vendor-card"
          data-tags="<?= h(implode(', ', array_filter([$vendor->product_tags, $vendor->market_weeks, $vendor->state_abbrs, $vendor->state_names, $vendor->cities]))) ?>">
          <div><img src="<?= h($vendor->profile_image ?? 'default.png') ?>" alt="Vendor Image"></div>
          <div>
            <h2><?= h($vendor->business_name) ?>, <?= h($vendor->state_abbrs) ?></h2>
          </div>
        </div>
      </a>
    <?php endforeach; ?>
  <?php else: ?>
    <p>No results found for "<?= h($searchTerm) ?>"</p>
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
