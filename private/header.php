<?php
if (!isset($page_title)) {
  $page_title = "Blue Ridge Bounty";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($page_title) ?></title>
  <link rel="stylesheet" href="<?= url_for('/css/styles.css') ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="/js/script.js" defer></script>
</head>

<body>

  <header>
    <h1>Blue Ridge Bounty</h1>
    <nav>
      <ul>
        <li><a href="<?= url_for('/index.php') ?>"><img src="<?= url_for('/img/assets/barn.png') ?>" alt="An icon of a barn" height="25" width="25"></a></li>
        <li><a href="<?= url_for('/schedule.php') ?>">Schedule</a></li>
        <li><a href="<?= url_for('/ourvendors.php') ?>">Our Vendors</a></li>
        <li><a href="<?= url_for('/aboutus.php') ?>">About Us</a></li>

        <?php if ($session->is_logged_in()) : ?>
          <?php if (isset($_SESSION['user_level_id']) && $_SESSION['user_level_id'] == 2) : ?>
            <li><a href="<?= url_for('vendor_dash.php') ?>">Dashboard</a></li>
          <?php else : ?>
            <li><a href="<?= url_for('/dashboard.php') ?>">Dashboard</a></li>
          <?php endif; ?>
          <li><a href="<?= url_for('/logout.php') ?>">Logout</a></li>
        <?php else : ?>
          <li><a href="<?= url_for('/login.php') ?>"><img src="<?= url_for('/img/assets/user.png') ?>" alt="A user login icon." height="25" width="25"></a></li>
        <?php endif; ?>



      </ul>
    </nav>
  </header>
