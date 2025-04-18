<?php require_once 'private/functions.php'; ?>

<?php if ($totalPages > 1): ?>
  <div class="pagination">
    <?php
    $base = $paginationBaseUrl ?? 'ourvendors.php';
    $query = [];

    if (!empty($vendor_id)) {
      $query['vendor_id'] = $vendor_id;
    }
    if (!empty($searchTerm)) {
      $query['search'] = $searchTerm;
    }

    $build_url = function ($pageNum) use ($base, $query) {
      $query['page'] = $pageNum;
      return $base . '?' . http_build_query($query);
    };
    ?>

    <?php if ($page > 1): ?>
      <a href="<?= $build_url($page - 1) ?>" aria-label="Previous Page">&laquo; Prev</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <a href="<?= $build_url($i) ?>" class="<?= $i === $page ? 'active' : '' ?>">
        <?= $i ?>
      </a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
      <a href="<?= $build_url($page + 1) ?>" aria-label="Next Page">Next &raquo;</a>
    <?php endif; ?>
  </div>
<?php endif; ?>
