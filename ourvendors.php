<?php
require 'private/cooper-taylor-db-connect.php';

try {
  $query = "SELECT user_id, business_name, description FROM vendor ORDER BY business_name ASC";
  $stmt = $conn->prepare($query);
  $stmt->execute();
  $vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Query failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Blue Ridge Bounty - Our Vendors</title>
  <link rel="stylesheet" href="/css/styles.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: Arial, sans-serif;
    }

    table {
      width: 90vw;
      border-collapse: collapse;
      margin: 0 auto;
    }

    th,
    td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: left;
    }

    th {
      background-color: #f4f4f4;
    }

    tr:nth-last-of-type(even) {
      color: #f4f4f4;
      background-color: rgba(48, 58, 43, 0.56);
    }
  </style>
</head>

<body>
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

  <main>
    <form action="" class="search-bar">
      <img src="img/assets/search.png" alt="A magnifying glass icon." height="25" width="25">
      <input type="text" name="search" placeholder="Search for vendors or products...">
    </form>

    <div>
      <table>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Description</th>
        </tr>
        <?php foreach ($vendors as $vendor): ?>
          <tr>
            <td><?= htmlspecialchars($vendor['user_id']); ?></td>
            <td><?= htmlspecialchars($vendor['business_name']); ?></td>
            <td><?= htmlspecialchars($vendor['description']); ?></td>
          </tr>
        <?php endforeach; ?>
      </table>

    </div>
  </main>



  <footer>
    <span>Blue Ridge Bounty &copy; 2025</span>
  </footer>
</body>

</html>
