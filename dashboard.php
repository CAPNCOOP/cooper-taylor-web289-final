<?php
require_once 'private/initialize.php'; // Include necessary setup files
require_once 'private/header.php';
$page_title = "Member - Dashboard"; // Set dynamic title
require_login(); // Ensure the user is logged in

// Fetch user info
$user_id = $_SESSION['user_id'];
$sql = "SELECT username, email, user_level_id FROM users WHERE user_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get user profile image
$sql = "SELECT file_path FROM profile_image WHERE user_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
$profile_image = $profile['file_path'] ?? 'img/upload/users/default.png';

// Determine user type
$user_type = ($user['user_level_id'] == 2) ? 'Vendor' : 'Member';

// Fetch saved vendors
$sql = "SHOW TABLES LIKE 'favorite'";
$stmt = $db->query($sql);
$table_exists = $stmt->fetchColumn();

$favorite = [];
if ($table_exists) {
  $sql = "SELECT v.vendor_id, v.business_name, pi.file_path AS profile_image
  FROM favorite f
  JOIN vendor v ON f.vendor_id = v.vendor_id
  LEFT JOIN profile_image pi ON v.user_id = pi.user_id
  WHERE f.user_id = ?";
  $stmt = $db->prepare($sql);
  $stmt->execute([$_SESSION['user_id']]);
  $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div id="user-profile">
  <h2>Hello, <?= htmlspecialchars($user['username']) ?>!</h2>
  <img src="<?= htmlspecialchars($profile_image) ?>" alt="Profile Picture" height="100" width="100">
  <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
  <p><strong>Account Type:</strong> <?= $user_type ?></p>
</div>

<div id="saved-vendors">
  <h2>Saved Vendors</h2>
  <?php if (!empty($favorites)): ?>
    <ul>
      <?php foreach ($favorites as $vendor): ?>
        <li>
          <img src="img/upload/users/<?= htmlspecialchars($vendor['profile_image'] ?? 'default.png') ?>"
            height="50" width="50" alt="Vendor Image">
          <a href="vendor_profile.php?vendor_id=<?= $vendor['vendor_id'] ?>">
            <?= htmlspecialchars($vendor['business_name']) ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p>No saved vendors yet.</p>
  <?php endif; ?>
</div>


</body>

</html>
