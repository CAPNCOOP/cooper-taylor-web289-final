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
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="<?= url_for('/css/styles.css') ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="/js/script.js" defer></script>
</head>


<body class="<?= strtolower(str_replace(' ', '-', $page_title ?? 'Blue Ridge Bounty')) ?>">


  <body class="<?= strtolower(str_replace(' ', '-', $page_title ?? 'Blue Ridge Bounty')) ?>">


    <header>
      <div>
        <div>
          <a href="<?= url_for('/index.php') ?>">
            <img src="img/assets/brblogo2.png" alt="" height="115" width="274">
          </a>
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
      </div>
      <div id="nav-history">
        <button onclick="window.history.back()" <?= empty($_SERVER['HTTP_REFERER']) ? 'disabled' : '' ?>>&#11164</button>
        <button onclick="window.history.forward()">&#11166</button>

        <?php include 'private/breadcrumbs.php'; ?>

        <nav id="breadcrumbs" aria-label="Breadcrumb">
          <ul>
            <?php foreach ($_SESSION['breadcrumbs'] as $index => $crumb): ?>
              <li>
                <?php if ($index !== array_key_last($_SESSION['breadcrumbs'])): ?>
                  <a href="<?= htmlspecialchars($crumb['path']) ?>">
                    <?= htmlspecialchars($crumb['label']) ?>
                  </a>
                <?php else: ?>
                  <span><?= htmlspecialchars($crumb['label']) ?></span>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
        </nav>
      </div>
    </header>
