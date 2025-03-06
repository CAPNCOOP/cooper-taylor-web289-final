<?php
$page_title = "Admin Dashboard";
require_once 'private/initialize.php';
require_once 'private/header.php';

// Restrict access to admins only (user_level_id = 3)
if (!isset($_SESSION['user_id']) || $_SESSION['user_level_id'] != 3) {
  header("Location: login.php");
  exit();
}

// Fetch all users (excluding admins and super admins)
$sql = "SELECT u.user_id, u.first_name, u.last_name, u.email, u.user_level_id, u.is_active 
        FROM users u 
        WHERE u.user_level_id = 1"; // Members only
$stmt = $db->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch vendors (user_level_id = 2)
$sql = "SELECT v.vendor_id, v.business_name, u.user_id, u.first_name, u.last_name, u.email, u.is_active
        FROM vendor v
        JOIN users u ON v.user_id = u.user_id
        WHERE u.user_level_id = 2";
$stmt = $db->prepare($sql);
$stmt->execute();
$vendor_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
  <h2>Welcome, Admin</h2>

  <?php if (isset($_GET['message'])): ?>
    <div class="notification">
      <?= htmlspecialchars($_GET['message']) ?>
    </div>
  <?php endif; ?>

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
          <th>Name</th>
          <th>Email</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($vendor_list as $vendor): ?>
          <tr>
            <td><?= htmlspecialchars($vendor['first_name'] . " " . $vendor['last_name']) ?></td>
            <td><?= htmlspecialchars($vendor['email']) ?></td>
            <td><?= $vendor['is_active'] ? 'Active' : 'Inactive' ?></td>
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
            <td>
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
