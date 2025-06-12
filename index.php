<?php
session_start();  
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Senyawa EO | Event Organizer</title>

  <!-- Swiper CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <!-- Custom External CSS -->
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <?php 
    include_once __DIR__ . '/sections/header.php'; 
    include_once __DIR__ . '/sections/home.php'; 
    include_once __DIR__ . '/sections/services-section.php'; 
    include_once __DIR__ . '/sections/about-section.php'; 
    include_once __DIR__ . '/sections/gallery-section.php'; 
    include_once __DIR__ . '/sections/price-section.php'; 
    include_once __DIR__ . '/sections/footer.php'; 
  ?>

  <!-- Swiper JS -->
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script>
    var swiper = new Swiper(".home-slider", {
      effect: "coverflow",
      grabCursor: true,
      centeredSlides: true,
      slidesPerView: "auto",
      coverflowEffect: {
        rotate: 0,
        stretch: 0,
        depth: 100,
        modifier: 2,
        slideShadows: true,
      },
      loop: true,
      autoplay: {
        delay: 4000,
        disableOnInteraction: false,
      },
    });
  </script>
</body>
</html>
