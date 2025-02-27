<?php
require_once 'private/initialize.php'; // Include necessary setup files
require_login(); // Ensure the user is logged in

// Fetch user info
$user_id = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? 'Guest';
$profile_image = $_SESSION['profile_image'] ?? 'img/upload/users/default.png';

// Fetch saved vendors
$sql = "SELECT v.vendor_id, v.business_name, v.city, v.state_id 
        FROM vendor v 
        JOIN favorites f ON v.vendor_id = f.vendor_id 
        WHERE f.user_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Dashboard - <?= htmlspecialchars($username) ?></title>
  <link rel="stylesheet" href="styles/dashboard.css">
</head>

<body class="user-dash">
  <header>
    <h1>Blue Ridge Bounty</h1>
    <nav>
      <ul>
        <li><a href="index.php"><img src="img/assets/barn.png" alt="An icon of a barn" height="25" width="25"></a></li>
        <li><a href="schedule.php">Schedule</a></li>
        <li><a href="ourvendors.php">Our Vendors</a></li>
        <li><a href="aboutus.php">About Us</a></li>
        <li><a href="login.php"><img src="img/assets/user.png" alt="A user login icon." height="25" width="25"></a></li>
      </ul>
    </nav>
  </header>

  <section class="favorites">
    <h2>Saved Vendors</h2>
    <?php if (!empty($favorites)): ?>
      <ul>
        <?php foreach ($favorites as $vendor): ?>
          <li>
            <strong><?= htmlspecialchars($vendor['business_name']) ?></strong><br>
            <?= htmlspecialchars($vendor['city']) ?>, <?= htmlspecialchars($vendor['state_id']) ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p>You have not saved any vendors yet.</p>
    <?php endif; ?>
  </section>

</body>

</html>
