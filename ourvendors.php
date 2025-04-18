<?php
$page_title = "Our Vendors";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_once 'private/functions.php';

$itemsPerPage = 12;
$page = (int)($_GET['page'] ?? 1);
$offset = ($page - 1) * $itemsPerPage;
$searchTerm = $_GET['search'] ?? '';

$vendors = Vendor::fetchApprovedVendorsWithTags($offset, $itemsPerPage, $searchTerm);
$totalVendors = Vendor::countApprovedVendors($searchTerm);
$totalPages = ceil($totalVendors / $itemsPerPage);
?>

<div id="vendorhead">
  <h2>Our Vendors</h2>
  <form method="GET" action="ourvendors.php" role="search">
    <input type="text" id="searchBar" name="search" placeholder="Search vendors, products, locations..." value="<?= h($searchTerm) ?>">
    <button type="submit" aria-label="Search Input">Search</button>
  </form>
</div>

<?php include 'private/pagination.php'; ?>

<div id="vendor-list">
  <?php if (!empty($vendors)): ?>
    <?php foreach ($vendors as $vendor): ?>
      <a href="vendor_profile.php?vendor_id=<?= h($vendor->vendor_id) ?>" aria-label="View Vendor Profile">
        <div class="vendor-card"
          data-tags="<?= h(implode(', ', array_filter([
                        $vendor->product_tags,
                        $vendor->market_weeks,
                        $vendor->state_abbrs,
                        $vendor->state_names,
                        $vendor->cities
                      ]))) ?>">
          <div><img src="img/upload/<?= h($vendor->profile_image ?? 'users/default.webp') ?>" alt="Profile Image">
          </div>
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

<?php include 'private/pagination.php'; ?>
<?php require_once 'private/footer.php'; ?>
