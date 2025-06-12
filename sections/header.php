<?php

?>

<header class="header">
  <a href="#" class="logo">
    <img src="/senyawa-wo/sections/img/logo.png" alt="Logo senyawa" style="height: 65px;">

  </a>
  <nav class="navbar">
    <a href="#home" class="nav-double" data-url="/senyawa-wo/">Home</a>
    <a href="#service" class="nav-double" data-url="/senyawa-wo/sections/services.php">Services</a>
    <a href="#about" class="nav-double" data-url="/senyawa-wo/sections/about.php">About</a>
    <a href="#gallery" class="nav-double" data-url="/senyawa-wo/sections/gallery.php">Gallery</a>
    <a href="#price" class="nav-double" data-url="/senyawa-wo/sections/price.php">Price</a>


    <?php
    if (isset($_SESSION['user']) && isset($_SESSION['role'])) {
      if ($_SESSION['role'] == 'admin') {
        echo '<a href="/senyawa-wo/dashboard.php" class="btn-dashboard">Dashboard Admin</a>';
        echo '<a href="/senyawa-wo/pages/logout.php" class="btn-logout">Logout</a>';
      } else {
        echo '<a href="/senyawa-wo/sections/profile.php" class="btn-profile">Profile</a>';
        echo '<a href="/senyawa-wo/pages/logout.php" class="btn-logout">Logout</a>';
      }
    } else {
      echo '<a href="/senyawa-wo/pages/login.php" class="btn-login">Login</a>';
    }
    ?>
  </nav>
</header>

<script>
  document.querySelectorAll('a.nav-double').forEach(link => {
    let clickCount = 0;
    let clickTimer = null;

    link.addEventListener('click', function(event) {
      event.preventDefault();

      clickCount++;

      if (clickCount === 1) {
        // Klik pertama: scroll ke section
        clickTimer = setTimeout(() => {
          const targetSection = this.getAttribute('href');
          const sectionElement = document.querySelector(targetSection);

          if (sectionElement) {
            sectionElement.scrollIntoView({
              behavior: 'smooth',
              block: 'start'
            });
          }

          clickCount = 0;
        }, 300);

      } else if (clickCount === 2) {
        // Double click: redirect ke halaman
        clearTimeout(clickTimer);
        clickCount = 0;

        const targetUrl = this.getAttribute('data-url');
        if (targetUrl) {
          window.location.href = targetUrl;
        }
      }
    });
  });
</script>