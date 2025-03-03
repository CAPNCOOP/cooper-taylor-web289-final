<?php
$page_title = "Admin Dashboard";
require_once 'private/initialize.php';

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['user_level_id'] != 3) { // 3 = Admin
  header("Location: login.php");
  exit();
}

require_once 'private/header.php';

// Fetch all users (except super admins)
$sql = "SELECT user_id, first_name, last_name, email, user_level_id, is_active 
        FROM users WHERE user_level_id != 4";
$stmt = $db->query($sql);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all vendors
$sql = "SELECT v.vendor_id, v.business_name, u.first_name, u.last_name, u.email, v.vendor_status
        FROM vendor v
        JOIN users u ON v.user_id = u.user_id
        WHERE u.user_level_id = 2"; // Only vendors
$stmt = $db->query($sql);
$vendor_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch vendor RSVPs for specific weeks
$sql = "SELECT v.vendor_id, v.business_name, u.first_name, u.last_name, u.email, vm.status AS rsvp_status,
               mw.week_start, mw.week_end
        FROM vendor v
        JOIN users u ON v.user_id = u.user_id
        JOIN vendor_market vm ON v.vendor_id = vm.vendor_id
        JOIN market_week mw ON vm.week_id = mw.week_id
        WHERE u.user_level_id = 2 AND mw.week_start >= CURDATE()
        ORDER BY mw.week_start ASC";
$stmt = $db->query($sql);
$vendor_rsvps = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch market weeks
$sql = "SELECT mw.week_id, mw.week_start, mw.week_end, mw.confirmation_deadline 
        FROM market_week mw
        ORDER BY mw.week_start ASC";
$stmt = $db->query($sql);
$market_weeks = $stmt->fetchAll(PDO::FETCH_ASSOC); // for markets



?>

<main>
  <h2>Welcome, Admin</h2>

  <div id="manage-users">
    <h3>Manage Users (Members)</h3>
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $user): ?>
          <tr>
            <td><?= htmlspecialchars($user['first_name'] . " " . $user['last_name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= $user['is_active'] ? 'Active' : 'Inactive' ?></td>
            <td>
              <a href="toggle_user.php?id=<?= $user['user_id'] ?>&action=deactivate">Deactivate</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div id="manage-vendors">
    <h3>Manage Vendors</h3>
    <table>
      <thead>
        <tr>
          <th>Vendor Name</th>
          <th>Email</th>
          <th>Market Week</th>
          <th>RSVP Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($vendor_rsvps as $vendor): ?>
          <tr>
            <td><?= htmlspecialchars($vendor['first_name'] . " " . $vendor['last_name']) ?></td>
            <td><?= htmlspecialchars($vendor['email']) ?></td>
            <td><?= htmlspecialchars($vendor['week_start'] . " - " . $vendor['week_end']) ?></td>
            <td>
              <form method="POST" action="rsvp_action.php">
                <input type="hidden" name="vendor_id" value="<?= $vendor['vendor_id'] ?>">
                <select name="status" onchange="this.form.submit()">
                  <option value="planned" <?= $vendor['rsvp_status'] == 'planned' ? 'selected' : '' ?>>Planned</option>
                  <option value="confirmed" <?= $vendor['rsvp_status'] == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                  <option value="canceled" <?= $vendor['rsvp_status'] == 'canceled' ? 'selected' : '' ?>>Canceled</option>
                </select>
              </form>
            </td>
            <td>
              <a href="toggle_vendor.php?id=<?= $vendor['vendor_id'] ?>&action=deactivate">Deactivate</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>


  <section>
    <h3>Pending Vendor Approvals</h3>

    <?php
    $sql = "SELECT v.vendor_id, v.business_name, u.first_name, u.last_name, u.email, v.contact_number, v.vendor_status
  FROM vendor v 
  JOIN users u ON v.user_id = u.user_id 
  WHERE v.vendor_status IN ('pending', 'denied')";

    $stmt = $db->query($sql);
    $pending_vendors = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    ?>

    <?php if (empty($pending_vendors)): ?>
      <p>No vendors are currently pending approval.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>Business Name</th>
            <th>Owner</th>
            <th>Email</th>
            <th>Contact</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pending_vendors as $vendor): ?>
            <tr>
              <td><?= htmlspecialchars($vendor['business_name']) ?></td>
              <td><?= htmlspecialchars($vendor['first_name'] . " " . $vendor['last_name']) ?></td>
              <td><?= htmlspecialchars($vendor['email']) ?></td>
              <td><?= htmlspecialchars($vendor['contact_number']) ?></td>
              <td><?= htmlspecialchars($vendor['vendor_status']) ?></td>
              <td>
                <a href="approve_vendor.php?vendor_id=<?= $vendor['vendor_id'] ?>&action=approve" class="btn btn-success">Approve</a>
                <a href="approve_vendor.php?vendor_id=<?= $vendor['vendor_id'] ?>&action=reject" class="btn btn-danger">Reject</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>


  <section>
    <h3>Schedule a Market Week</h3>
    <form action="schedule_market.php" method="POST">
      <label for="week_start">Week Start:</label>
      <input type="date" id="week_start" name="week_start" required>

      <label for="week_end">Week End:</label>
      <input type="date" id="week_end" name="week_end" required>

      <label for="confirmation_deadline">RSVP Deadline:</label>
      <input type="date" id="confirmation_deadline" name="confirmation_deadline" required>

      <button type="submit">Schedule Market</button>
    </form>
  </section>


  <section>
    <h3>Update Homepage Content</h3>
    <form action="update_homepage.php" method="POST">
      <textarea name="homepage_content" rows="5" required></textarea>
      <button type="submit">Update Content</button>
    </form>
  </section>
</main>

<?php require_once 'private/footer.php'; ?>
