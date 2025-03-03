<?php
$page_title = "Market Schedule";
require_once 'private/initialize.php';
require_once 'private/header.php';

// Fetch upcoming markets
$sql = "SELECT week_id, week_start, week_end FROM market_week WHERE week_start >= CURDATE() ORDER BY week_start ASC";
$stmt = $db->query($sql);
$markets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Upcoming Markets</h2>
<table>
  <tr>
    <th>Market Week Start</th>
    <th>Market Week End</th>
    <th>Vendors Attending</th>
  </tr>
  <?php foreach ($markets as $market): ?>
    <tr>
      <td><?= htmlspecialchars($market['week_start']) ?></td>
      <td><?= htmlspecialchars($market['week_end']) ?></td>
      <td>
        <a href="market_vendors.php?week_id=<?= $market['week_id'] ?>">View Vendors</a>
      </td>
    </tr>
  <?php endforeach; ?>
</table>

<?php require_once 'private/footer.php'; ?>
