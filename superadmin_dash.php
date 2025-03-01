<?php
require_once 'private/initialize.php';
require_once 'private/header.php';

// Restrict access to super admins only
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_level_id'] != 4) { // 4 = Super Admin
  header("Location: login.php");
  exit();
}

$page_title = "Super Admin Dashboard";
require_once 'private/header.php';

// Fetch all users (including admins but excluding super admins)
$sql = "SELECT user_id, first_name, last_name, email, user_level_id, is_active FROM users WHERE user_level_id != 4";
$stmt = $db->query($sql);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
  <h2>Welcome, Super Admin</h2>

  <section>
    <h3>Manage Users & Admins</h3>
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

              <?php if ($user['user_level_id'] == 3): // Only show admin toggle for super admins 
              ?>
                <a href="toggle_admin.php?id=<?= $user['user_id'] ?>&action=demote" class="btn btn-warning">Demote</a>
              <?php elseif ($user['user_level_id'] == 1 || $user['user_level_id'] == 2): ?>
                <a href="toggle_admin.php?id=<?= $user['user_id'] ?>&action=promote" class="btn btn-primary">Promote to Admin</a>
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
    <h3>Update Homepage Content</h3>
    <form action="update_homepage.php" method="POST">
      <textarea name="homepage_content" rows="5" required></textarea>
      <button type="submit">Update Content</button>
    </form>
  </section>
</main>

<footer>
  <span>Blue Ridge Bounty &copy; 2025</span>
</footer>
</body>

</html>
