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
$stmt = $db->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all vendors (user_level_id = 2)
$sql = "SELECT v.vendor_id, v.business_name, u.user_id, u.first_name, u.last_name, u.email, u.is_active
        FROM vendor v
        JOIN users u ON v.user_id = u.user_id
        WHERE u.user_level_id = 2";

$stmt = $db->prepare($sql);
$stmt->execute();
$vendor_list = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Fetch vendor RSVPs grouped by vendor
$sql = "SELECT vm.vendor_id, mw.week_id, mw.week_start, mw.week_end, vm.status AS rsvp_status
        FROM vendor_market vm
        JOIN market_week mw ON vm.week_id = mw.week_id
        WHERE mw.week_start >= CURDATE()
        ORDER BY mw.week_start ASC";
$stmt = $db->prepare($sql);
$stmt->execute();
$vendor_rsvps = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organize RSVPs per vendor for easy lookup
$vendor_rsvp_map = [];
foreach ($vendor_rsvps as $rsvp) {
  $vendor_id = $rsvp['vendor_id'];
  if (!isset($vendor_rsvp_map[$vendor_id])) {
    $vendor_rsvp_map[$vendor_id] = [];
  }
  $vendor_rsvp_map[$vendor_id][] = [
    'week_id' => $rsvp['week_id'],
    'week_start' => $rsvp['week_start'],
    'week_end' => $rsvp['week_end'],
    'rsvp_status' => $rsvp['rsvp_status']
  ];
}

// Fetch available market weeks
$sql = "SELECT vm.vendor_id, mw.week_id, mw.week_start, mw.week_end
        FROM vendor_market vm
        JOIN market_week mw ON vm.week_id = mw.week_id
        WHERE vm.status = 'confirmed'
        ORDER BY mw.week_start ASC";
$stmt = $db->prepare($sql);
$stmt->execute();
$vendor_market_weeks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organize market weeks per vendor
$market_weeks_map = [];
foreach ($vendor_market_weeks as $week) {
  $market_weeks_map[$week['vendor_id']][] = [
    'week_id' => $week['week_id'],
    'week_start' => $week['week_start'],
    'week_end' => $week['week_end']
  ];
}

// Fetch vendor RSVPs grouped by vendor with available market weeks
$sql = "SELECT vm.vendor_id, mw.week_id, mw.week_start, mw.week_end, vm.status AS rsvp_status
        FROM vendor_market vm
        JOIN market_week mw ON vm.week_id = mw.week_id
        WHERE mw.week_start >= CURDATE()
        ORDER BY mw.week_start ASC";
$stmt = $db->prepare($sql);
$stmt->execute();
$vendor_rsvp_map = [];
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $rsvp) {
  $vendor_rsvp_map[$rsvp['vendor_id']][$rsvp['week_id']] = $rsvp['rsvp_status'];
}


?>

<main>
  <h2>Welcome, Super Admin</h2>

  <?php if (isset($_GET['message'])): ?>
    <div class="notification">
      <?= htmlspecialchars($_GET['message']) ?>
    </div>
  <?php endif; ?>

  <div id="notification" class="hidden"></div>
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
                <a href="toggle_user.php?id=<?= htmlspecialchars($user['user_id']) ?>&action=<?= $user['is_active'] ? 'deactivate' : 'activate' ?>"
                  class="btn <?= $user['is_active'] ? 'btn-danger' : 'btn-success' ?>">
                  <?= $user['is_active'] ? 'Deactivate' : 'Activate' ?>
                </a>
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
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($vendor_list as $vendor): ?>
          <tr>
            <td><?= htmlspecialchars($vendor['first_name'] . " " . $vendor['last_name']) ?></td>
            <td><?= htmlspecialchars($vendor['email']) ?></td>

            <!-- Market Week Selection -->
            <td>
              <form method="POST" action="update_rsvp.php">
                <input type="hidden" name="vendor_id" value="<?= $vendor['vendor_id'] ?>">
                <select name="week_id" onchange="this.form.submit()">
                  <option value="">Select a Week</option>
                  <?php if (!empty($market_weeks_map[$vendor['vendor_id']])): ?>
                    <?php foreach ($market_weeks_map[$vendor['vendor_id']] as $week): ?>
                      <option value="<?= $week['week_id'] ?>">
                        <?= htmlspecialchars($week['week_start'] . " - " . $week['week_end']) ?>
                      </option>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <option value="">No Confirmed Weeks</option>
                  <?php endif; ?>
                </select>
              </form>
            </td>

            <!-- RSVP Status Selection -->
            <td>
              <form method="POST" action="update_rsvp.php">
                <input type="hidden" name="vendor_id" value="<?= $vendor['vendor_id'] ?>">
                <input type="hidden" name="week_id" value="<?= isset($market_weeks_map[$vendor['vendor_id']][0]['week_id']) ? $market_weeks_map[$vendor['vendor_id']][0]['week_id'] : ''; ?>">

                <?php
                // Default status if no data is found
                $default_status = 'planned';

                // Ensure the vendor ID and week ID exist in the array before accessing it
                $selected_week_id = $market_weeks_map[$vendor['vendor_id']][0]['week_id'] ?? null;
                $current_status = $selected_week_id && isset($vendor_rsvp_map[$vendor['vendor_id']][$selected_week_id])
                  ? $vendor_rsvp_map[$vendor['vendor_id']][$selected_week_id]
                  : $default_status;
                ?>

                <select name="status" onchange="this.form.submit()">
                  <option value="planned" <?= $current_status == 'planned' ? 'selected' : '' ?>>Planned</option>
                  <option value="confirmed" <?= $current_status == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                  <option value="canceled" <?= $current_status == 'canceled' ? 'selected' : '' ?>>Canceled</option>
                </select>
              </form>
            </td>


            <!-- Activate/Deactivate Action -->
            <!-- Status Column -->
            <td><?= $vendor['is_active'] ? 'Active' : 'Inactive' ?></td>

            <!-- Activate/Deactivate Action -->
            <td>
              <?php
              $vendor_action = $vendor['is_active'] ? 'deactivate' : 'activate';
              $button_class = $vendor['is_active'] ? 'btn btn-danger' : 'btn btn-success';
              $button_text = $vendor['is_active'] ? 'Deactivate' : 'Activate';
              ?>
              <a href="toggle_user.php?id=<?= htmlspecialchars($vendor['user_id']) ?>&action=<?= htmlspecialchars($vendor_action) ?>"
                class="<?= htmlspecialchars($button_class) ?>">
                <?= htmlspecialchars($button_text) ?>
              </a>
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
                <a href="toggle_user.php?id=<?= htmlspecialchars($user['user_id']) ?>&action=<?= $user['is_active'] ? 'deactivate' : 'activate' ?>"
                  class="btn <?= $user['is_active'] ? 'btn-danger' : 'btn-success' ?>">
                  <?= $user['is_active'] ? 'Deactivate' : 'Activate' ?>
                </a>


              </td>
            </tr>
          <?php endif; ?>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>

  <!-- Pending Vendor Approvals Section -->
  <section>
    <h3>Pending Vendor Requests</h3>
    <table>
      <thead>
        <tr>
          <th>Vendor Name</th>
          <th>Email</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $sql = "SELECT v.vendor_id, v.business_name, u.email 
                    FROM vendor v
                    JOIN users u ON v.user_id = u.user_id
                    WHERE v.vendor_status = 'pending'";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $pending_vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($pending_vendors as $vendor):
        ?>
          <tr>
            <td><?= htmlspecialchars($vendor['business_name']) ?></td>
            <td><?= htmlspecialchars($vendor['email']) ?></td>
            <td class="request-action-column">
              <a href="approve_vendor.php?id=<?= $vendor['vendor_id'] ?>" class="btn btn-success">Approve</a>
              <a href="reject_vendor.php?id=<?= $vendor['vendor_id'] ?>" class="btn btn-danger">Reject</a>
            </td>
          </tr>
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
