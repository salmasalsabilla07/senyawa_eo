<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Our Services - Senyawa WO</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
  <link rel="stylesheet" href="../style.css">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #121212;
      color: #fff;
    }

    .services {
      padding: 80px 20px 40px;
      text-align: center;
      background: #222;
    }

    .services-title {
      font-size: 40px;
      font-weight: 700;
      letter-spacing: 1px;
      color: #ffffff;
      margin-bottom: 60px;
      position: relative;
      display: inline-block;
    }

    .services-title span {
      color: #fbb034;
    }

    .services-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 30px;
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
    }

    .service-card {
      background-color: #111;
      border-radius: 15px;
      padding: 30px 25px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
      transition: all 0.3s ease;
    }

    .service-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 6px 16px rgba(255, 179, 52, 0.4);
    }

    .service-icon {
      font-size: 48px;
      color: #fbb034;
      margin-bottom: 20px;
    }

    .service-title {
      font-size: 22px;
      font-weight: 600;
      color: #ffffff;
      margin-bottom: 15px;
    }

    .service-content {
      list-style: none;
      padding: 0;
      margin: 0;
      color: #d1d1d1;
      font-size: 15px;
    }

    .service-content li {
      margin-bottom: 10px;
      position: relative;
      padding-left: 20px;
    }

    .service-content li::before {
      content: "•";
      position: absolute;
      left: 0;
      color: #fbb034;
      font-size: 18px;
      top: 0;
      line-height: 1.2;
    }

    @media (max-width: 768px) {
      .services-title {
        font-size: 32px;
      }
      .service-title {
        font-size: 20px;
      }
    }
  </style>
</head>
<body>
<section id="services" class="services">
  <div class="services-title">OUR <span>SERVICE</span></div>

  <div class="services-container">
    <div class="service-card">
      <div class="service-icon"><i class="fas fa-calendar-alt"></i></div>
      <div class="service-title">Wedding Planning</div>
      <ul class="service-content">
        <li>Konsultasi konsep, tema, dan alur acara.</li>
        <li>Rekomendasi vendor (dekorasi, make-up, catering, dll).</li>
      </ul>
    </div>

    <div class="service-card">
      <div class="service-icon"><i class="fas fa-paint-brush"></i></div>
      <div class="service-title">Venue Decoration</div>
      <ul class="service-content">
        <li>Dekorasi sesuai tema (rustic, elegan, modern, adat).</li>
        <li>Pelaminan, photobooth, meja tamu.</li>
        <li>Tim rundown teknis hari-H.</li>
      </ul>
    </div>

    <div class="service-card">
      <div class="service-icon"><i class="fas fa-camera"></i></div>
      <div class="service-title">Vendor Management</div>
      <ul class="service-content">
        <li>Koordinasi vendor.</li>
        <li>Dokumentasi & hiburan (MC, band, foto-video).</li>
      </ul>
    </div>

    <div class="service-card">
      <div class="service-icon"><i class="fas fa-gift"></i></div>
      <div class="service-title">Custom Packages</div>
      <ul class="service-content">
        <li>Paket hemat & premium sesuai budget.</li>
      </ul>
    </div>
  </div>
</section>

</body>
</html>
