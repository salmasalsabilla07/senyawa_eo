<section class="home" id="home">
  <div class="content">
    <h1>SENYAWA WEDDING ORGANIZER</h1>
    <h2>Your Perfect Wedding, Our Perfect Plan</h2>
    <a href="sections/price.php" class="btn">Booking Now</a>
  </div>

  <div class="home-slider swiper">
    <div class="swiper-wrapper">
      <?php
      for ($i = 1; $i <= 5; $i++) {
        echo '<div class="swiper-slide">
                <img src="sections/images/home' . $i . '.jpg" alt="Slide ' . $i . '">
              </div>';
      }
      ?>
    </div>
  </div>
</section>
