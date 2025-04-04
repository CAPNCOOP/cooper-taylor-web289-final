<?php
$page_title = "Super Admin Dashboard";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_once 'private/functions.php';

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
$sql = "SELECT v.vendor_id, v.business_name, v.vendor_status, u.user_id, u.first_name, u.last_name, u.email, u.is_active
        FROM vendor v
        JOIN users u ON v.user_id = u.user_id
        WHERE u.user_level_id = 2";

$stmt = $db->prepare($sql);
$stmt->execute();
$vendor_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all admins separately (user_level_id = 3)
$sql = "SELECT u.user_id, u.first_name, u.last_name, u.email, u.is_active 
        FROM users u 
        WHERE u.user_level_id = 3"; // Only fetch admins
$stmt = $db->prepare($sql);
$stmt->execute();
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch vendor RSVPs grouped by vendor
$sql = "SELECT vm.vendor_id, mw.week_id, mw.week_start, mw.week_end, vm.status AS rsvp_status
        FROM vendor_market vm
        JOIN market_week mw ON vm.week_id = mw.week_id
        WHERE mw.week_start >= CURDATE() AND mw.is_deleted = 0
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
        WHERE vm.status = 'confirmed' AND mw.is_deleted = 0
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
        WHERE mw.week_start >= CURDATE() AND mw.is_deleted = 0
        ORDER BY mw.week_start ASC";
$stmt = $db->prepare($sql);
$stmt->execute();
$vendor_rsvp_map = [];
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $rsvp) {
  $vendor_rsvp_map[$rsvp['vendor_id']][$rsvp['week_id']] = $rsvp['rsvp_status'];
}

// Fetch all upcoming market weeks (week_end in future)
$sql = "SELECT week_id, week_start, week_end, market_status 
        FROM market_week 
        WHERE week_end >= CURDATE() AND is_deleted = 0
        ORDER BY week_end ASC";
$stmt = $db->prepare($sql);
$stmt->execute();
$upcoming_markets = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Fetch homepage welcome message
$sql = "SELECT content FROM homepage_content WHERE section = 'welcome' LIMIT 1";
$stmt = $db->prepare($sql);
$stmt->execute();
$homepage_content = $stmt->fetchColumn();
if (!$homepage_content) {
  $homepage_content = "Welcome to Blue Ridge Bounty! 🌻";
}

?>

<main>
  <h2>Welcome, Super Admin</h2>

  <?php require_once 'private/popup_message.php'; ?>

  <!-- Manage Users Section -->
  <section>
    <div class="section-header" data-section="manage-users" onclick="toggleSection(this)">
      <h3>Manage Users</h3>
    </div>
    <div class="section-content">
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
              <td><span class="card-info">Name: </span><?= h($user['first_name'] . " " . $user['last_name']) ?></td>
              <td><span class="card-info">Email: </span><?= h($user['email']) ?></td>
              <td><span class="card-info">Status: </span><?= $user['is_active'] ? 'Active' : 'Inactive' ?></td>
              <td>
                <a href="toggle_entity.php?id=<?= $user['user_id'] ?>&action=<?= $user['is_active'] ? 'deactivate' : 'activate' ?>&type=user">
                  <?= $user['is_active'] ? 'Deactivate User' : 'Activate User' ?>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <!-- Manage Vendors Section -->
  <section>
    <div class="section-header" data-section="manage-vendors" onclick="toggleSection(this)">
      <h3>Manage Vendors</h3>
    </div>
    <div class="section-content">
      <table>
        <thead>
          <tr>
            <th>Vendor Name</th>
            <th>Email</th>
            <th>RSVP Status</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($vendor_list as $vendor): ?>
            <tr>
              <td><span class="card-info">Name: </span><?= h($vendor['first_name'] . " " . $vendor['last_name']) ?></td>
              <td><span class="card-info">Email: </span><?= h($vendor['email']) ?></td>
              <!-- RSVP Status Selection -->
              <td>
                <span class="card-info">Market Status: </span>
                <form method="POST" action="update_rsvp.php">
                  <input type="hidden" name="vendor_id" value="<?= h($vendor['vendor_id']) ?>">
                  <select name="week_id" required>
                    <option value="">Select a Week</option>
                    <?php if (!empty($market_weeks_map[$vendor['vendor_id']])): ?>
                      <?php foreach ($market_weeks_map[$vendor['vendor_id']] as $week): ?>
                        <option value="<?= h($week['week_id']) ?>"
                          <?= isset($selected_week_id) && $selected_week_id == $week['week_id'] ? 'selected' : '' ?>>
                          <?= h($week['week_start'] . " - " . $week['week_end']) ?>
                        </option>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <option value="">No Confirmed Weeks</option>
                    <?php endif; ?>
                  </select>
                  <?php
                  // Ensure $current_status is always set
                  $selected_week_id = $market_weeks_map[$vendor['vendor_id']][0]['week_id'] ?? null;
                  $default_status = 'planned'; // Set default status
                  $current_status = $selected_week_id && isset($vendor_rsvp_map[$vendor['vendor_id']][$selected_week_id])
                    ? $vendor_rsvp_map[$vendor['vendor_id']][$selected_week_id]
                    : $default_status;
                  ?>
                  <select name="status" required>
                    <option value="planned" <?= $current_status == 'planned' ? 'selected' : '' ?>>Planned</option>
                    <option value="confirmed" <?= $current_status == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                    <option value="canceled" <?= $current_status == 'canceled' ? 'selected' : '' ?>>Canceled</option>
                  </select>
                  <button type="submit">Update</button>
                </form>
              </td>

              <!-- Status Column -->
              <td><span class="card-info">Status: </span><?= $vendor['is_active'] ? 'Active' : 'Inactive' ?></td>
              <!-- Activate/Deactivate Action -->
              <td>
                <?php
                $vendor_action = ($vendor['is_active'] == 1) ? 'deactivate' : 'activate';
                $button_class = ($vendor['is_active'] == 1) ? 'btn btn-danger' : 'btn btn-success';
                $button_text = ($vendor['is_active'] == 1) ? 'Deactivate Vendor' : 'Activate Vendor';
                ?>
                <a href="toggle_entity.php?id=<?= $vendor['user_id'] ?>&action=<?= $vendor_action ?>&type=vendor"
                  class="<?= $button_class ?>">
                  <?= $button_text ?>
                </a>
              </td>

            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <!-- Manage Admins Section -->
  <section>
    <div class="section-header" data-section="manage-admins" onclick="toggleSection(this)">
      <h3>Manage Admins</h3>
    </div>
    <div class="section-content">
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
          <?php foreach ($admins as $admin): ?>
            <tr>
              <td><span class="card-info">Name: </span><?= h($admin['first_name'] . " " . $admin['last_name']) ?></td>
              <td><span class="card-info">Email: </span><?= h($admin['email']) ?></td>
              <td><span class="card-info">Role: </span>Admin</td>
              <td><span class="card-info">Status: </span><?= $admin['is_active'] ? 'Active' : 'Inactive' ?></td>
              <td>
                <a href="toggle_entity.php?id=<?= h($admin['user_id']) ?>&action=<?= $admin['is_active'] ? 'deactivate' : 'activate' ?>&type=admin"
                  class="btn <?= $admin['is_active'] ? 'btn-danger' : 'btn-success' ?>">
                  <?= $admin['is_active'] ? 'Deactivate' : 'Activate' ?>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <!-- Pending Vendor Approvals Section -->
  <section>
    <div class="section-header" data-section="pending-approvals" onclick="toggleSection(this)">
      <h3>Pending Vendor Requests</h3>
    </div>
    <div class="section-content">
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
                  WHERE v.vendor_status = 'pending' OR v.vendor_status = 'denied'";
          $stmt = $db->prepare($sql);
          $stmt->execute();
          $pending_vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);
          foreach ($pending_vendors as $vendor):
          ?>
            <tr>
              <td><?= h($vendor['business_name']) ?></td>
              <td><?= h($vendor['email']) ?></td>
              <td class="request-action-column">
                <a href="approve_vendor.php?vendor_id=<?= h($vendor['vendor_id']) ?>&action=approve" class="btn btn-success">Approve</a>
                <a href="approve_vendor.php?vendor_id=<?= h($vendor['vendor_id']) ?>&action=reject" class="btn btn-danger">Reject</a>
              </td>

            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <!-- Schedule Market Week Section -->
  <section>
    <div class="section-header" data-section="schedule-market" onclick="toggleSection(this)">
      <h3>Schedule a Market Week</h3>
    </div>
    <div class="section-content">
      <form action="schedule_market.php" method="POST">
        <fieldset>
          <label for="week_start">Week Start:</label>
          <input type="date" id="week_start" name="week_start" required>
        </fieldset>
        <fieldset>
          <label for="week_end">Week End:</label>
          <input type="date" id="week_end" name="week_end" required>
        </fieldset>
        <fieldset>
          <label for="confirmation_deadline">RSVP Deadline:</label>
          <input type="date" id="confirmation_deadline" name="confirmation_deadline" required>
        </fieldset>
        <button type="submit">Schedule Market</button>
      </form>
    </div>
  </section>

  <!-- Manage Markets Section -->
  <section>
    <div class="section-header" data-section="manage-markets" onclick="toggleSection(this)">
      <h3>Manage Scheduled Markets</h3>
    </div>
    <div class="section-content">
      <table>
        <thead>
          <tr>
            <th>Week Start</th>
            <th>Market Day</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($upcoming_markets as $market): ?>
            <tr>
              <td><?= h($market['week_start']) ?></td>
              <td><?= h($market['week_end']) ?></td>
              <td><?= ucfirst(h($market['market_status'])) ?></td>
              <td>
                <?php if ($market['market_status'] === 'confirmed'): ?>
                  <form action="toggle_market_status.php" method="POST" style="display:inline-block">
                    <input type="hidden" name="week_id" value="<?= h($market['week_id']) ?>">
                    <input type="hidden" name="new_status" value="cancelled">
                    <button type="submit" class="btn btn-warning">Cancel Market</button>
                  </form>
                <?php elseif ($market['market_status'] === 'cancelled'): ?>
                  <form action="toggle_market_status.php" method="POST" style="display:inline-block">
                    <input type="hidden" name="week_id" value="<?= h($market['week_id']) ?>">
                    <input type="hidden" name="new_status" value="confirmed">
                    <button type="submit" class="btn btn-success">Reconfirm Market</button>
                  </form>
                <?php endif; ?>

                <form action="delete_market.php" method="POST" style="display:inline-block" onsubmit="return confirm('Are you sure you want to permanently delete this market?');">
                  <input type="hidden" name="week_id" value="<?= h($market['week_id']) ?>">
                  <button type="submit" class="btn btn-danger">Delete</button>
                </form>
              </td>

            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <!-- Edit Homepage Message -->
  <section>
    <div class="section-header" data-section="update-content" onclick="toggleSection(this)">
      <h3>Edit Homepage Welcome Message</h3>
    </div>
    <div class="section-content">
      <form method="POST" action="update_homepage.php">
        <textarea name="content" rows="5" cols="50" required><?= h($homepage_content) ?></textarea>
        <input type="hidden" name="section" value="welcome">
        <button type="submit">Update Content</button>
      </form>
    </div>
  </section>

</main>

<?php require_once 'private/footer.php'; ?>
