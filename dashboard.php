<?php
$page_title = "Member - Dashboard";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_login(); // Ensure the user is logged in
$user_level = $_SESSION['user_level_id'] ?? null;

// Fetch user info
$user_id = $_SESSION['user_id'];
$sql = "SELECT username, email, user_level_id FROM users WHERE user_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get user profile image
$sql = "SELECT first_name, last_name FROM users WHERE user_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id]);
$user_info = $stmt->fetch(PDO::FETCH_ASSOC);

// Create possible file paths for each image type
$profile_image_base = 'img/upload/users/' . strtolower($user_info['first_name'] . '_' . $user_info['last_name']);
$accepted_formats = ['.jpg', '.png', '.webp'];

// Initialize the profile image variable to the default image
$profile_image = 'img/upload/users/default.png';

// Loop through accepted formats to find a valid profile image
foreach ($accepted_formats as $format) {
  $image_path = $profile_image_base . $format;
  if (file_exists($image_path)) {
    $profile_image = $image_path;
    break;
  }
}

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
  <a href="edit_profile.php" class="btn">Edit Profile</a>
</div>

<div id="saved-vendors">
  <h2>Saved Vendors</h2>
  <?php if (!empty($favorites)): ?>
    <ul>
      <?php foreach ($favorites as $vendor): ?>
        <li>
          <img src="img/upload/users/<?= htmlspecialchars($vendor['profile_image'] ?? 'default.png') ?>"
            height="100" width="100" alt="Vendor Image">
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

<?php require_once 'private/footer.php'; ?>
