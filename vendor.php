<?php
require_once 'private/initialize.php';

// Fetch all approved vendors
$sql = "SELECT vendor_id, business_name, vendor_bio, profile_image FROM vendor WHERE vendor_status = 'approved'";
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

<body>
  <h1>Our Vendors</h1>

  <input type="text" id="searchBar" placeholder="Search vendors, products, markets..." onkeyup="filterVendors()">

  <div id="vendor-list">
    <?php foreach ($vendors as $vendor): ?>
      <div class="vendor-card" onclick="window.location.href='vendor.php?id=<?php echo $vendor['vendor_id']; ?>'">
        <img src="uploads/<?php echo htmlspecialchars($vendor['profile_image']); ?>" alt="Vendor Image">
        <h2><?php echo htmlspecialchars($vendor['business_name']); ?></h2>
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
