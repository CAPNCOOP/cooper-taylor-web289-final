<?php
require_once 'private/initialize.php';
require_once 'private/header.php';
$page_title = "Home"; // Set dynamic title
require_once 'private/initialize.php';
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
?>

<main>
  <section>
    <h2>Who We Are...</h2>
    <div>
      <div class="aboutus-item">
        <img src="img/assets/farmer4.jpg" alt="A photo of a local farmer." height="450" width="300">
        <p>At Blue Ridge Bounty, we believe that food should be more than just sustenance—it should be a connection to the land, the farmers who cultivate it, and the community that gathers around it. Founded with a passion for local, sustainable agriculture, our farmers market is a vibrant hub where fresh, non-GMO produce, pasture-raised meats, and handcrafted goods come together in celebration of real food and honest craftsmanship.</p>
      </div>
      <div class="aboutus-item">
        <img src="img/assets/flour.jpg" alt="A photo of a man preparing bread." height="450" width="300">
        <p>Our mission is simple: support local growers, promote sustainable practices, and provide our community with fresh, responsibly sourced food. Every vendor at our markets is dedicated to transparency and quality, ensuring that what ends up on your table is wholesome, nutritious, and grown with care. From the crisp apples harvested in nearby orchards to the artisanal breads baked with locally milled flour, every product tells a story of hard work, passion, and commitment to a better food system.</p>
      </div>
      <div class="aboutus-item">
        <img src="img/assets/farmer7.jpg" alt="A photo of a local farmer." height="450" width="300">
        <p>Beyond just a marketplace, Blue Ridge Bounty is a place where relationships are formed—where you can meet the people who grow your food, learn their farming philosophies, and feel good about the choices you make. Whether you're a lifelong advocate for farm-to-table living or just starting your journey toward more mindful eating, we welcome you to be part of our community.</p>
      </div>


      <!-- Booooo 👎-->
      <!-- <div>
            <h3 id="contact">Contact Us</h3>
            <div>
              <img src="img/assets/envelope.png" alt="An envelope icon." height="25" width="25">
              <span>support@blueridgebounty.us</span>
            </div>
            <div>
              <img src="img/assets/phone.png" alt="An envelope icon." height="25" width="25">
              <span>1-258-2689</span>
            </div>
          </div>
        </div> -->
  </section>
</main>

<footer>
  <span>Blue Ridge Bounty &copy; 2025</span>
</footer>
</body>
