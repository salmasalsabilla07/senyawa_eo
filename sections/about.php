<?php
session_start(); // supaya session tetap aktif dan kamu tetap bisa cek login kalau mau
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About Us - Senyawa WO</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="../style.css" />
</head>
<body>

<?php include 'header.php'; ?>

<section class="about" id="about">
  <h1 class="heading">about <span>us</span></h1>
  <div class="row">
    <div class="image">
      <img src="img/about.jpg" alt="About Image" />
    </div>
    <div class="content">
      <h3>Pernikahan Sempurna Anda, Rencana Sempurna Kami</h3>
      <p>Senyawa EO resmi launching pada 15 September 2023 sebagai penyedia jasa event organizer profesional. Kami siap menghadirkan pelayanan terbaik untuk setiap klien.</p>
      <p>Tim kami terus mengembangkan kualitas dan berkolaborasi dengan vendor terbaik demi menjadikan setiap acara berkesan dan bernilai tinggi.</p>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>

</body>
</html>
