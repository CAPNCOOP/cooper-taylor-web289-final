<?php
$page_title = "Home";
require_once 'private/initialize.php';
require_once 'private/header.php';
?>

<div class="hero-image">
  <div>
    <h2>Welcome to the Heart of the Harvest</h2>

    <p><strong>Blue Ridge Bounty</strong> isn’t just a market. It’s a community. A tradition. A Saturday morning ritual where roots run deep — in the ground, and between neighbors. We're glad you're here. Let’s grow something together. Want to know more?</p>
    <div><a href="aboutus.php">Click here!</a></div>
  </div>
</div>

<main>

  <div class="slideshow-container">
    <div class="mySlides fade">
      <div class="numbertext">1/6</div>
      <picture>
        <source srcset="img/assets/field-1000w.webp" media="(min-width: 1200px)">
        <source srcset="img/assets/field-800w.webp" media="(min-width: 992px)">
        <source srcset="img/assets/field-600w.webp" media="(min-width: 768px)">
        <source srcset="img/assets/field-400w.webp" media="(min-width: 480px)">
        <img src="img/assets/field.webp" alt="A field on a lush green farm." width="900" height="600" loading="lazy" onerror="this.onerror=null;this.src='img/assets/field.jpg';">
      </picture>
      <div class="text"><strong>Non-GMO & Sustainably Grown</strong> – Our vendors are committed to natural farming practices, ensuring you get the purest, most flavorful ingredients without harmful chemicals or genetic modifications.</div>
    </div>

    <div class="mySlides fade">
      <picture>
        <source srcset="img/assets/chicken1-1000w.webp" media="(min-width: 1200px)">
        <source srcset="img/assets/chicken1-800w.webp" media="(min-width: 992px)">
        <source srcset="img/assets/chicken1-600w.webp" media="(min-width: 768px)">
        <source srcset="img/assets/chicken1-400w.webp" media="(min-width: 480px)">
        <img src="img/assets/chicken1.webp" alt="A farmer holding two chickens." width="900" height="600" loading="lazy" onerror="this.onerror=null;this.src='img/assets/chicken1.jpg';">
      </picture>
      <div class="numbertext">2/6</div>
      <div class="text"><strong>Farm-Raised & Pasture-Fed</strong> – From grass-fed beef to free-range eggs, we bring you high-quality, ethically raised meats straight from the farm.</div>
    </div>

    <div class="mySlides fade">
      <picture>
        <source srcset="img/assets/pastries-1000w.webp" media="(min-width: 1200px)">
        <source srcset="img/assets/pastries-800w.webp" media="(min-width: 992px)">
        <source srcset="img/assets/pastries-600w.webp" media="(min-width: 768px)">
        <source srcset="img/assets/pastries-400w.webp" media="(min-width: 480px)">
        <img src="img/assets/pastries.webp" alt="A booth at a farmers market." width="900" height="600" loading="lazy" onerror="this.onerror=null;this.src='img/assets/pastries.jpg';">
      </picture>
      <div class="numbertext">3/6</div>
      <div class="text"><strong>Artisan & Handmade Goods</strong> – Beyond produce, you&apos;ll find fresh-baked bread, local honey, handcrafted soaps, and more—all made with love by local makers.</div>
    </div>

    <div class="mySlides fade">
      <picture>
        <source srcset="img/assets/farmer10-1000w.webp" media="(min-width: 1200px)">
        <source srcset="img/assets/farmer10-800w.webp" media="(min-width: 992px)">
        <source srcset="img/assets/farmer10-600w.webp" media="(min-width: 768px)">
        <source srcset="img/assets/farmer10-400w.webp" media="(min-width: 480px)">
        <img src="img/assets/farmer10.webp" alt="A farmer tends to some crops." width="900" height="600" loading="lazy" onerror="this.onerror=null;this.src='img/assets/farmer10.jpg';">
      </picture>
      <div class="numbertext">4/6</div>
      <div class="text"><strong>Know Your Farmers, Know Your Food</strong> – Here, you can meet the growers, ask questions, and feel confident about what you&apos;re putting on your plate.</div>
    </div>

    <div class="mySlides fade">
      <picture>
        <source srcset="img/assets/produce-1000w.webp" media="(min-width: 1200px)">
        <source srcset="img/assets/produce-800w.webp" media="(min-width: 992px)">
        <source srcset="img/assets/produce-600w.webp" media="(min-width: 768px)">
        <source srcset="img/assets/produce-400w.webp" media="(min-width: 480px)">
        <img src="img/assets/produce.webp" alt="A produce stand at a market." width="900" height="600" loading="lazy" onerror="this.onerror=null;this.src='img/assets/produce.jpg';">
      </picture>
      <div class="numbertext">5/6</div>
      <div class="text"><strong>Looking for something specific?</strong> Use our search feature to find vendors selling your favorite items at the next market!</div>
    </div>

    <div class="mySlides fade">
      <picture>
        <source srcset="img/assets/booth-1000w.webp" media="(min-width: 1200px)">
        <source srcset="img/assets/booth-800w.webp" media="(min-width: 992px)">
        <source srcset="img/assets/booth-600w.webp" media="(min-width: 768px)">
        <source srcset="img/assets/booth-400w.webp" media="(min-width: 480px)">
        <img src="img/assets/booth.webp" alt="A booth at a farmers market." width="900" height="600" loading="lazy" onerror="this.onerror=null;this.src='img/assets/booth.jpg';">
      </picture>
      <div class="numbertext">6/6</div>
      <div class="text"><strong>Want to be a vendor?</strong> Learn how you can join our community of growers and makers. <a href="vendorsignup.php">Click here!</a></div>
    </div>


    <!-- arrow buttons -->
    <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
    <a class="next" onclick="plusSlides(1)">&#10095;</a>

    <!-- dots -->
    <div style="text-align:center">
      <span class="dot" onclick="currentSlide(1)"></span>
      <span class="dot" onclick="currentSlide(2)"></span>
      <span class="dot" onclick="currentSlide(3)"></span>
      <span class="dot" onclick="currentSlide(4)"></span>
      <span class="dot" onclick="currentSlide(5)"></span>
      <span class="dot" onclick="currentSlide(6)"></span>
    </div>
  </div>
</main>

<?php require_once 'private/footer.php'; ?>
