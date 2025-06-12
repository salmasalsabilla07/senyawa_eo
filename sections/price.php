<?php
session_start();
$logged_in = isset($_SESSION['user']) && !empty($_SESSION['user']);
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Harga Paket - Senyawa WO</title>
  <link rel="stylesheet" href="../style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body>

  <?php include 'header.php'; ?>

  <section class="price" id="price">
    <h1 class="heading">OUR <span>PRICING</span></h1>
    <div class="box-container">

      <?php
      $config_file = '';
      $possible_paths = [
        '../pages/config.php',
        '../../pages/config.php',
        './pages/config.php',
        '../admin/config.php',
        '../../admin/config.php',
        '../config.php',
        '../../config.php',
        './config.php',
        $_SERVER['DOCUMENT_ROOT'] . '/senyawa-wo/config.php',
        $_SERVER['DOCUMENT_ROOT'] . '/config.php'
      ];

      foreach ($possible_paths as $path) {
        if (file_exists($path)) {
          $config_file = $path;
          break;
        }
      }

      if ($config_file) {
        include($config_file);
      } else {
        $conn = new mysqli("localhost", "root", "", "senyawa_wo");
        if ($conn->connect_error) {
          echo "<p>Koneksi database gagal: " . $conn->connect_error . "</p>";
        }
      }

      $manual_packages = [
        ["Engagement Regular", "Rp. 1.250.000", [
          "Crew 2 orang (PIC Event, Show Director)",
          "Konsultasi Konsep",
          "Rundown Acara",
          "Games",
          "Free Bouqet bunga",
          "Free MC"
        ]],
        ["Engagement VIP", "Rp. 1.850.000", [
          "Crew 4 orang (PIC Event, Show Director, Cheeker, PIC Media)",
          "Konsultasi Konsep",
          "Rundown Acara",
          "Games",
          "Free Bouqet bunga",
          "Free Undangan Digital",
          "Free Video Moment",
          "Free MC"
        ]],
        ["Akad Nikah Reguler", "Rp. 2.250.000", [
          "Crew 4 orang (PIC Event, Show Director, Cheeker, PIC Tamu)",
          "Konsultasi Konsep",
          "Rundown Acara",
          "Teks Sungkeman",
          "Teks Mohon Restu",
          "Ten Card",
          "Short Video Moment",
          "Free MC"
        ]],
        ["Akad Nikah & Beda Hari", "Rp. 500.000", []],
      ];

      $use_manual_data = true;

      if (isset($conn) && $conn instanceof mysqli && !$conn->connect_error) {
        $result = $conn->query("SELECT * FROM packages ORDER BY price ASC");
        if ($result && $result->num_rows > 0) {
          $use_manual_data = false;
          while ($package = $result->fetch_assoc()) {
            $features = explode("\n", $package['description']);
            echo '<div class="box">';
            echo '<h3 class="tittle">' . htmlspecialchars($package['name']) . '</h3>';
            echo '<h3 class="amount">Rp ' . number_format($package['price'], 0, ',', '.') . '</h3>';
            echo '<ul>';
            foreach ($features as $feature) {
              if (trim($feature) != "") {
                echo '<li><i class="fas fa-check"></i> ' . htmlspecialchars(trim($feature)) . '</li>';
              }
            }
            echo '</ul>';
            echo '<form action="../pages/chekout.php" method="POST" style="margin-top: 10px;" onsubmit="return checkLogin();">';
            echo '<input type="hidden" name="package_id" value="' . $package['id'] . '">';
            echo '<button type="submit" class="btn">check out</button>';
            echo '</form>';
            echo '</div>';
          }
        }
      }

      if ($use_manual_data) {
        foreach ($manual_packages as $pkg) {
          echo '<div class="box">';
          echo '<h3 class="tittle">' . htmlspecialchars($pkg[0]) . '</h3>';
          echo '<h3 class="amount">' . htmlspecialchars($pkg[1]) . '</h3>';
          echo '<ul>';
          foreach ($pkg[2] as $item) {
            echo '<li><i class="fas fa-check"></i> ' . htmlspecialchars($item) . '</li>';
          }
          echo '</ul>';
          echo '<form action="../pages/select_package.php" method="GET" style="margin-top: 10px;" onsubmit="return checkLogin();">';
          echo '<input type="hidden" name="package_name" value="' . htmlspecialchars($pkg[0]) . '">';
          echo '<input type="hidden" name="package_price" value="' . htmlspecialchars($pkg[1]) . '">';
          echo '<button type="submit" class="btn">check out</button>';
          echo '</form>';
          echo '</div>';
        }
      }

      if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
      }
      ?>

    </div>
  </section>

  <?php include 'footer.php'; ?>

  <script>
    function checkLogin() {
      const isLoggedIn = <?php echo json_encode($logged_in); ?>;
      if (!isLoggedIn) {
        alert("Please login first before checking out!");
        return false;
      }
      return true;
    }
  </script>

</body>

</html>
