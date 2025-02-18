<?php
require_once 'private/initialize.php';

// Ensure user is logged in and is a vendor
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit("Redirecting to login...");
}

// Fetch vendor details
$user_id = $_SESSION['user_id'];
$sql = "SELECT vendor_id FROM vendor WHERE user_id = ? AND vendor_status = 'approved'";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id]);
$vendor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vendor) {
  header("Location: index.php");
  exit("Access denied: Vendor approval required.");
}
$vendor_id = $vendor['vendor_id'];

// Fetch upcoming markets
$sql = "SELECT market_id, name, market_date FROM market WHERE market_date >= CURDATE() ORDER BY market_date ASC";
$stmt = $db->query($sql);
$markets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch vendor's existing RSVPs
$sql = "SELECT market_id, status FROM vendor_market WHERE vendor_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$vendor_id]);
$rsvp_status = $stmt->fetchAll(PDO::FETCH_ASSOC);
$rsvp_map = array_column($rsvp_status, 'status', 'market_id');

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>RSVP for Markets</title>
  <link rel="stylesheet" href="css/styles.css">
</head>

<body>
  <h1>RSVP for Upcoming Markets</h1>
  <table>
    <tr>
      <th>Market Name</th>
      <th>Date</th>
      <th>RSVP Status</th>
    </tr>
    <?php foreach ($markets as $market): ?>
      <tr>
        <td><?php echo htmlspecialchars($market['name']); ?></td>
        <td><?php echo htmlspecialchars($market['market_date']); ?></td>
        <td>
          <form method="post" action="rsvp_action.php">
            <input type="hidden" name="market_id" value="<?php echo $market['market_id']; ?>">
            <select name="status" onchange="this.form.submit()">
              <option value="planned" <?php echo isset($rsvp_map[$market['market_id']]) && $rsvp_map[$market['market_id']] == 'planned' ? 'selected' : ''; ?>>Planned</option>
              <option value="confirmed" <?php echo isset($rsvp_map[$market['market_id']]) && $rsvp_map[$market['market_id']] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
              <option value="canceled" <?php echo isset($rsvp_map[$market['market_id']]) && $rsvp_map[$market['market_id']] == 'canceled' ? 'selected' : ''; ?>>Canceled</option>
            </select>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</body>

</html>
