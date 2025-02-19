<?php
require_once 'private/initialize.php';

// Fetch all approved vendors
$sql = "SELECT v.vendor_id, v.business_name, v.vendor_bio, pi.file_path AS profile_image
FROM vendor v
LEFT JOIN profile_image pi ON v.user_id = pi.user_id
LEFT JOIN product p ON v.vendor_id = p.vendor_id
LEFT JOIN vendor_market vm ON v.vendor_id = vm.vendor_id
LEFT JOIN market m ON vm.market_id = m.market_id
WHERE v.vendor_status = 'approved'
GROUP BY v.vendor_id";
$stmt = $db->query($sql);
$vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Our Vendors</title>
  <link rel="stylesheet" href="css/styles.css">
</head>

<body class="ourvendors">
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


  <div id="vendorhead">
    <h2>Our Vendors</h2>
    <input type="text" id="searchBar" placeholder="Search vendors, products, markets..." onkeyup="filterVendors()">
  </div>

  <div id="vendor-list">
    <?php foreach ($vendors as $vendor): ?>
      <div class="vendor-card" onclick="window.location.href='vendor_profile.php?id=<?php echo $vendor['vendor_id']; ?>'">
        <h2><?php echo htmlspecialchars($vendor['business_name']); ?></h2>
        <img src="img/upload/users/<?php echo htmlspecialchars($vendor['profile_image'] ?? 'default.png'); ?>" height="250" width="250" alt="Vendor Image">
        <p><?php echo nl2br(htmlspecialchars($vendor['vendor_bio'])); ?></p>
      </div>
    <?php endforeach; ?>
  </div>

  <script>
    function filterVendors() {
      let input = document.getElementById('searchBar').value.toLowerCase();
      let vendorCards = document.querySelectorAll('.vendor-card');
      vendorCards.forEach(card => {
        let name = card.querySelector('h2').innerText.toLowerCase();
        let tags = card.getAttribute('data-tags') ? card.getAttribute('data-tags').toLowerCase() : '';
        let desc = card.querySelector('p').innerText.toLowerCase();
        if (name.includes(input) || desc.includes(input)) {
          card.style.display = "block";
        } else {
          card.style.display = "none";
        }
      });
    }
  </script>
</body>

</html>
