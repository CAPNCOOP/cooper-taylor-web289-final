<?php
$page_title = "Market RSVP";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_login(); // Ensure the user is logged in

$user_id = $_SESSION['user_id'];

// Fetch vendor details
$sql = "SELECT vendor_id FROM vendor WHERE user_id = ? AND vendor_status = 'approved'";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id]);
$vendor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vendor) {
  header("Location: index.php");
  exit("Access denied: Vendor approval required.");
}

$vendor_id = $vendor['vendor_id'];

// Fetch all upcoming market weeks
$sql = "SELECT mw.week_id, mw.week_start, mw.week_end, mw.confirmation_deadline
        FROM market_week mw
        WHERE mw.week_start >= CURDATE()
        ORDER BY mw.week_start ASC";

$stmt = $db->query($sql);
$weeks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch vendor's existing RSVPs
$sql = "SELECT week_id, status FROM vendor_market WHERE vendor_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$vendor_id]);
$rsvp_status = $stmt->fetchAll(PDO::FETCH_ASSOC);
$rsvp_map = array_column($rsvp_status, 'status', 'week_id');
?>

<main>
  <h2>RSVP for Upcoming Markets</h2>

  <?php require_once 'private/popup_message.php'; ?>

  <?php if (empty($weeks)): ?>
    <p>No upcoming markets available.</p>
  <?php else: ?>
    <table>
      <tr>
        <th>Week Start</th>
        <th>Week End</th>
        <th>RSVP Status</th>
        <th>Deadline</th>
        <th>Action</th>
      </tr>

      <?php foreach ($weeks as $week): ?>
        <tr>
          <td><?= htmlspecialchars($week['week_start']) ?></td>
          <td><?= htmlspecialchars($week['week_end']) ?></td>
          <td>
            <?= isset($rsvp_map[$week['week_id']]) ? ucfirst($rsvp_map[$week['week_id']]) : 'Not RSVPed' ?>
          </td>
          <td><?= htmlspecialchars($week['confirmation_deadline']) ?></td>
          <td>
            <?php if ($week['confirmation_deadline'] >= date('Y-m-d')): ?>
              <form method="post" action="rsvp_action.php">
                <input type="hidden" name="week_id" value="<?= $week['week_id']; ?>">
                <select name="status" onchange="this.form.submit()">
                  <option value="planned" <?= isset($rsvp_map[$week['week_id']]) && $rsvp_map[$week['week_id']] == 'planned' ? 'selected' : ''; ?>>Planned</option>
                  <option value="confirmed" <?= isset($rsvp_map[$week['week_id']]) && $rsvp_map[$week['week_id']] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                  <option value="canceled" <?= isset($rsvp_map[$week['week_id']]) && $rsvp_map[$week['week_id']] == 'canceled' ? 'selected' : ''; ?>>Canceled</option>
                </select>
              </form>
            <?php else: ?>
              <span style="color: gray;">RSVP Closed</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>

    </table>
  <?php endif; ?>

</main>

<?php require_once 'private/footer.php'; ?>
