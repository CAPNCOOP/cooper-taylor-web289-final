<?php
$page_title = "Page Not Found";
http_response_code(404);
require_once 'private/initialize.php';
require_once 'private/header.php';

// Array of your custom 404 image filenames
$images = [
  '4041.webp',
  '4042.webp',
  '4043.webp',
];

// Pick a random image
$selected_image = $images[array_rand($images)];
?>

<main class="error-404">
  <section class="error-container">
    <h1>404 - Page Not Found</h1>
    <p>Sorry, friend. That page either vanished or never existed.</p>

    <?php
    // Image selection (without extension or size)
    $base_images = ['4041', '4042', '4043'];
    $selected_base = $base_images[array_rand($base_images)];
    ?>

    <img
      src="img/assets/<?= $selected_base ?>-800w.webp"
      srcset="
    img/assets/<?= $selected_base ?>-200w.webp 200w,
    img/assets/<?= $selected_base ?>-400w.webp 400w,
    img/assets/<?= $selected_base ?>-600w.webp 600w,
    img/assets/<?= $selected_base ?>-800w.webp 800w,
    img/assets/<?= $selected_base ?>-1000w.webp 1000w,
    img/assets/<?= $selected_base ?>-1200w.webp 1200w
  "
      sizes="(max-width: 600px) 90vw, (max-width: 1000px) 80vw, 600px"
      alt="Funny 404 image"
      class="error-illustration">


    <a href="index.php" class="btn">Back to Home</a>
  </section>
</main>

<?php require_once 'private/footer.php'; ?>
