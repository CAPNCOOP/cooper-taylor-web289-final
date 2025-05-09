<?php
$page_title = "About Us";
$meta_description = "Cultivating connection through local food and good soil.";
$og_image = "'https://blueridgebounty.us/img/assets/aboutus-thumb.webp";
require_once 'private/initialize.php';
require_once 'private/header.php';
?>

<main role="main">
  <section>
    <h2>Who We Are...</h2>
    <div>
      <div class="aboutus-item">
        <picture>
          <source srcset="img/assets/farmer4-1000h.webp" media="(min-width: 1200px)">
          <source srcset="img/assets/farmer4-800h.webp" media="(min-width: 992px)">
          <source srcset="img/assets/farmer4-600h.webp" media="(min-width: 768px)">
          <source srcset="img/assets/farmer4-400h.webp" media="(min-width: 480px)">
          <img src="img/assets/farmer4.webp" alt="A photo of a local farmer." width="667" height="1000" loading="lazy" onerror="this.onerror=null;this.src='img/assets/farmer4.jpg';">
        </picture>
        <p>At <strong>Blue Ridge Bounty</strong>, we believe that food should be more than just sustenance—it should be a connection to the land, the farmers who cultivate it, and the community that gathers around it. Founded with a passion for local, sustainable agriculture, our farmers market is a vibrant hub where fresh, non-GMO produce, pasture-raised meats, and handcrafted goods come together in celebration of real food and honest craftsmanship.</p>
      </div>

      <div class="aboutus-item">
        <picture>
          <source srcset="img/assets/flour-1000h.webp" media="(min-width: 1200px)">
          <source srcset="img/assets/flour-800h.webp" media="(min-width: 992px)">
          <source srcset="img/assets/flour-600h.webp" media="(min-width: 768px)">
          <source srcset="img/assets/flour-400h.webp" media="(min-width: 480px)">
          <img src="img/assets/flour.webp" alt="A photo of a man preparing bread." width="667" height="1000" loading="lazy" onerror="this.onerror=null;this.src='img/assets/flour.jpg';">
        </picture>
        <p><strong>Our mission is simple:</strong> support local growers, promote sustainable practices, and provide our community with fresh, responsibly sourced food. Every vendor at our markets is dedicated to transparency and quality, ensuring that what ends up on your table is wholesome, nutritious, and grown with care. From the crisp apples harvested in nearby orchards to the artisanal breads baked with locally milled flour, every product tells a story of hard work, passion, and commitment to a better food system.</p>
      </div>

      <div class="aboutus-item">
        <picture>
          <source srcset="img/assets/farmer7-1000h.webp" media="(min-width: 1200px)">
          <source srcset="img/assets/farmer7-800h.webp" media="(min-width: 992px)">
          <source srcset="img/assets/farmer7-600h.webp" media="(min-width: 768px)">
          <source srcset="img/assets/farmer7-400h.webp" media="(min-width: 480px)">
          <img src="img/assets/farmer7.webp" alt="A photo of a local farmer." width="667" height="1000" loading="lazy" onerror="this.onerror=null;this.src='img/assets/farmer7.jpg';">
        </picture>
        <p><strong>Beyond just a marketplace</strong>, Blue Ridge Bounty is a place where relationships are formed—where you can meet the people who grow your food, learn their farming philosophies, and feel good about the choices you make. Whether you're a lifelong advocate for farm-to-table living or just starting your journey toward more mindful eating, we welcome you to be part of our community.</p>
      </div>

      <div class="aboutus-item">
        <h2>Want to join our community?</h2>
        <p>If you're a local <strong>grower, maker, or artisan</strong> with something special to share, we'd love to hear from you. We're always looking for passionate new vendors to join our vibrant market family. Apply today and help us keep the spirit of local thriving — one booth at a time. Whether you're harvesting heirloom tomatoes, baking sourdough at sunrise, or crafting something completely unique, there's a place for you here. Our market isn't just a place to sell — it's a space to connect, share your story, and grow right alongside the community.</p>
        <a href=<?= url_for('/vendorsignup.php') ?> aria-label="Apply to be Vendor">Apply Now!</a>
      </div>

      <div id="contact">
        <h3>Contact Us</h3>
        <div>
          <img src="img/assets/envelope.png" alt="An envelope icon." height="25" width="25" loading="lazy">
          <span>support@blueridgebounty.us</span>
        </div>
        <div>
          <img src="img/assets/phone.png" alt="An envelope icon." height="25" width="25" loading="lazy">
          <span>1-828-874-2689</span>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require_once 'private/footer.php'; ?>
