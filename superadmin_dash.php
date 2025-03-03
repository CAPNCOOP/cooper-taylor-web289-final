<?php
$page_title = "Super Admin Dashboard";
require_once 'private/initialize.php';
require_once 'private/header.php';

// Restrict access to super admins only
if (!isset($_SESSION['user_id']) || $_SESSION['user_level_id'] != 4) { // 4 = Super Admin
  header("Location: login.php");
  exit();
}

// Fetch all users (excluding super admins)
$sql = "SELECT u.user_id, u.first_name, u.last_name, u.email, u.user_level_id, u.is_active 
        FROM users u 
        WHERE u.user_level_id != 4"; // Exclude super admins
$stmt = $db->query($sql);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all vendors (user_level_id = 2)
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
$market_weeks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
  <h2>Welcome, Super Admin</h2>

  <!-- Manage Users Section -->
  <section>
    <h3>Manage Users</h3>
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $user): ?>
          <?php if ($user['user_level_id'] == 1): // Only display members 
          ?>
            <tr>
              <td><?= htmlspecialchars($user['first_name'] . " " . $user['last_name']) ?></td>
              <td><?= htmlspecialchars($user['email']) ?></td>
              <td>Member</td>
              <td><?= $user['is_active'] ? 'Active' : 'Inactive' ?></td>
              <td>
                <a href="toggle_user.php?id=<?= $user['user_id'] ?>&action=deactivate" class="btn btn-danger">Deactivate</a>
              </td>
            </tr>
          <?php endif; ?>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>

  <!-- Manage Vendors Section -->
  <section>
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
  </section>

  <!-- Manage Admins Section -->
  <section>
    <h3>Manage Admins</h3>
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $user): ?>
          <?php if ($user['user_level_id'] == 3): // Only display admins 
          ?>
            <tr>
              <td><?= htmlspecialchars($user['first_name'] . " " . $user['last_name']) ?></td>
              <td><?= htmlspecialchars($user['email']) ?></td>
              <td>Admin</td>
              <td><?= $user['is_active'] ? 'Active' : 'Inactive' ?></td>
              <td>
                <a href="toggle_user.php?id=<?= $user['user_id'] ?>&action=deactivate" class="btn btn-danger">Deactivate</a>
              </td>
            </tr>
          <?php endif; ?>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>

  <!-- Schedule Market Week Section -->
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

  <!-- Update Homepage Content Section -->
  <section>
    <h3>Update Homepage Content</h3>
    <form action="update_homepage.php" method="POST">
      <textarea name="homepage_content" rows="5" required></textarea>
      <button type="submit">Update Content</button>
    </form>
  </section>
</main>

<?php require_once 'private/footer.php'; ?>
