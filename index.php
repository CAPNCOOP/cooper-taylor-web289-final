<?php
require_once 'private/initialize.php';
require_once 'private/header.php';
$page_title = "Home"; // Set dynamic title
?>

<div class="hero-image">
  <section class="typewriter-wrapper">
    <span class="static-text">Blue Ridge Bounty Is...</span>
    <div class="typewriter">
      <span id="text"></span>
    </div>
  </section>
</div>

<div id="content-wrapper">
  <main>
    <div>
      <p>Nestled in the heart of the Blue Ridge Mountains, Blue Ridge Bounty is your gateway to the freshest, locally sourced produce, farm-raised meats, artisan goods, and handcrafted delights. We believe in real food, real people, and real connections—which is why every item you find here is grown, raised, or made with care by passionate farmers and artisans in our community.</p>
    </div>

    <div class="slideshow-container">
      <div class="mySlides fade">
        <div class="numbertext">1/6</div>
        <img src="img/assets/field.jpg" alt="A field on a lush green farm." height="600" width="900">
        <div class="text"><strong>Non-GMO & Sustainably Grown</strong> – Our vendors are committed to natural farming practices, ensuring you get the purest, most flavorful ingredients without harmful chemicals or genetic modifications.</div>
      </div>
      <div class="mySlides fade">
        <img src="img/assets/chicken1.jpg" alt="A farmer holding two chickens." height="600" width="900">
        <div class="numbertext">2/6</div>
        <div class="text"><strong>Farm-Raised & Pasture-Fed</strong> – From grass-fed beef to free-range eggs, we bring you high-quality, ethically raised meats straight from the farm.</div>
      </div>
      <div class="mySlides fade">
        <img src="img/assets/pastries.jpg" alt="A booth at a farmers market." height="600" width="900">
        <div class="numbertext">3/6</div>
        <div class="text"><strong>Artisan & Handmade Goods</strong> – Beyond produce, you’ll find fresh-baked bread, local honey, handcrafted soaps, and more—all made with love by local makers.</div>
      </div>
      <div class="mySlides fade">
        <img src="img/assets/farmer10.jpg" alt="A farmer tends to some crops." height="600" width="900">
        <div class="numbertext">4/6</div>
        <div class="text"><strong>Know Your Farmers, Know Your Food</strong> – Know Your Farmers, Know Your Food – Here, you can meet the growers, ask questions, and feel confident about what you’re putting on your plate.</div>
      </div>
      <div class="mySlides fade">
        <img src="img/assets/produce.jpg" alt="A produce stand at a market." height="600" width="900">
        <div class="numbertext">5/6</div>
        <div class="text"><strong>Looking for something specific?</strong> Use our search feature to find vendors selling your favorite items at the next market!</div>
      </div>
      <div class="mySlides fade">
        <img src="img/assets/booth.jpg" alt="A booth at a farmers market." height="600" width="900">
        <div class="numbertext">6/6</div>
        <div class="text"><strong>Want to be a vendor?</strong> Learn how you can join our community of growers and makers. <a href="login.php">Click here!</a></div>
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
</div>

<footer>
  <span>Blue Ridge Bounty &copy; 2025</span>
  <a href="aboutus.php#contact">Contact Us</a>
</footer>
</body>

</html>
