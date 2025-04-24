<?php
$page_title = "Market Schedule";
$meta_description = "Plan your visit, taste what’s in season.";
$og_image = "'https://blueridgebounty.us/img/assets/market-schedule-thumb.webp"; // update domain later
require_once 'private/initialize.php';
require_once 'private/map_header.php';

// Fetch upcoming markets
$sql = "SELECT mw.week_id, mw.week_start,
    DATE_FORMAT(DATE_ADD(mw.week_start, INTERVAL 6 DAY), '%b-%d-%Y') AS saturday_market_date,
    mw.confirmation_deadline
    FROM market_week mw
    WHERE DATE_ADD(mw.week_start, INTERVAL 6 DAY) >= CURDATE()
    AND mw.is_deleted = 0
    ORDER BY mw.week_start ASC";

$stmt = $db->query($sql);
$markets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Markets are every Saturday 8am-2pm, Pack Square Park</h2>

<div id="schedule-map-container">
  <div id="map"></div>
  <div id="schedule-div">
    <h2>Upcoming Markets</h2>
    <table>
      <thead>
        <tr>
          <th>Market Date</th>
          <th>Vendors Attending</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($markets as $market): ?>
          <tr>
            <td><?= htmlspecialchars($market['saturday_market_date']) ?></td>
            <td>
              <a href="market_vendors.php?week_id=<?= $market['week_id'] ?>">View Vendors</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    var map = L.map('map').setView([35.595527869870025, -82.54932889086415], 17); // Coordinates for Asheville, NC

    // Add OpenStreetMap tiles to the map
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Add a marker (pin) to the map
    var marker = L.marker([35.595527869870025, -82.54932889086415]).addTo(map);
    marker.bindPopup("<b>Blue Ridge Bounty</b><br>80 Court Plaza, Asheville, NC").openPopup();
  </script>
</div>

<?php require_once 'private/footer.php'; ?>
