<?php
if (!isset($page_title)) {
  $page_title = "Blue Ridge Bounty";
  require_once 'private/functions.php';
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title><?= h($page_title) ?></title>
  <link rel="stylesheet" href="<?= url_for('/css/styles.css') ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="/js/script.js" defer></script>
  <link rel="icon" href="/favicon.ico" type="image/x-icon">
</head>

<body class="<?= strtolower(str_replace(' ', '-', $page_title ?? 'Blue Ridge Bounty')) ?>">


  <header>
    <div>
      <a href="<?= url_for('/index.php') ?>">
        <div>
          <h1>Blue Ridge Bounty</h1>
          <span>Farmers Market</span>
        </div>
      </a>
      <div id="nav-history">
        <button onclick="window.history.back()" <?= empty($_SERVER['HTTP_REFERER']) ? 'disabled' : '' ?>>&#11164</button>
        <button onclick="window.history.forward()">&#11166</button>
      </div>
    </div>

    <div>
      <nav class="nav-links">
        <ul>
          <li><a href="<?= url_for('/schedule.php') ?>">Schedule</a></li>
          <li><a href="<?= url_for('/ourvendors.php') ?>">Our Vendors</a></li>
          <li><a href="<?= url_for('/aboutus.php') ?>">About Us</a></li>
          <li><a href="<?= url_for('/aboutus.php#contact') ?>">Contact Us</a></li>

          <?php if ($session->is_logged_in()) : ?>
            <?php if (!empty($_SESSION['user_level_id'])) : ?>
              <?php if ($_SESSION['user_level_id'] == 2) : ?>
                <li><a href="<?= url_for('/vendor_dash.php') ?>">Dashboard</a></li>
              <?php elseif ($_SESSION['user_level_id'] == 3) : ?>
                <li><a href="<?= url_for('/admin_dash.php') ?>">Dashboard</a></li>
              <?php elseif ($_SESSION['user_level_id'] == 4) : ?>
                <li><a href="<?= url_for('/superadmin_dash.php') ?>">Dashboard</a></li>
              <?php else : ?>
                <li><a href="<?= url_for('/dashboard.php') ?>">Dashboard</a></li>
              <?php endif; ?>
            <?php else : ?>
              <li><a href="<?= url_for('/dashboard.php') ?>">Dashboard</a></li>
            <?php endif; ?>

            <li>
              <a href="<?= url_for('/logout.php') ?>">
                Logout, <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>
              </a>
            </li>

          <?php else : ?>
            <li><a href="<?= url_for('/login.php') ?>">Log In</a></li>
          <?php endif; ?>
        </ul>
      </nav>
      <span id="menu">
        Menu
      </span>
    </div>
  </header>
