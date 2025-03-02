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
$sql = "SELECT user_id, first_name, last_name, email, user_level_id, is_active FROM users WHERE user_level_id != 4";
$stmt = $db->query($sql);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
  <h2>Welcome, Admin</h2>

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
            <td>
              <?php
              switch ($user['user_level_id']) {
                case 1:
                  echo "Member";
                  break;
                case 2:
                  echo "Vendor";
                  break;
                case 3:
                  echo "Admin";
                  break;
                default:
                  echo "Unknown";
              }
              ?>
            </td>
            <td><?= $user['is_active'] ? "Active" : "Inactive" ?></td>
            <td>
              <?php if ($user['is_active']): ?>
                <a href="toggle_user.php?id=<?= $user['user_id'] ?>&action=deactivate" class="btn btn-danger">Deactivate</a>
              <?php else: ?>
                <a href="toggle_user.php?id=<?= $user['user_id'] ?>&action=activate" class="btn btn-success">Activate</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>

  <section>
    <h3>Pending Vendor Approvals</h3>

    <?php
    $sql = "SELECT v.vendor_id, v.business_name, u.first_name, u.last_name, u.email, v.contact_number, v.vendor_status
    FROM vendor v 
    JOIN users u ON v.user_id = u.user_id 
    WHERE v.vendor_status IN ('pending', 'denied')";

    $stmt = $db->query($sql);
    $pending_vendors = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

    if (!$pending_vendors) { // Ensure it's always an array
      $pending_vendors = [];
    }
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
