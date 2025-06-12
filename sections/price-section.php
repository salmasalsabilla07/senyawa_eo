<section class="price" id="price" style="text-align: center; padding: 40px 20px;">

  <!-- Internal CSS Styling -->
  <style>

/* === PRICE SECTION === */
.price {
  padding: 60px 20px;
  background-color: #222;
  text-align: center;
  color: white;
}

.price .heading {
  font-size: 2rem;
  margin-bottom: 2rem;
  color: white;
}

.price .heading span {
  color: var(--theme-color); /* Misalnya: #f3b343 */
}

/* === BOX CONTAINER === */
.price .box-container {
  display: grid;
  grid-template-columns: repeat(4, 1fr); /* 4 boxes in one row */
  gap: 2rem;
  padding: 0 2rem 4rem;
}

/* === INDIVIDUAL CARD === */
.price .box {
  background-color: #111;
  border: 1px solid #333;
  border-radius: 20px;
  padding: 1.5rem; /* Reduced padding */
  box-shadow: 0 5px 15px rgba(255, 255, 255, 0.05);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.price .box:hover {
  transform: translateY(-10px);
  box-shadow: 0 8px 20px rgba(243, 179, 67, 0.2); /* theme color glow */
}

/* === TITLE & PRICE === */
.price .box .tittle {
  font-size: 1.2rem; /* Smaller title font */
  color: var(--theme-color);
  margin-bottom: 0.5rem;
  font-weight: bold;
}

.price .box .amount {
  font-size: 1.4rem; /* Smaller amount font */
  color: #fff;
  margin-bottom: 1.5rem;
  font-weight: bold;
}

/* === LIST === */
.price .box ul {
  list-style: none;
  padding-left: 0;
  margin-bottom: 2rem;
  text-align: left;
}

.price .box ul li {
  margin: 0.6rem 0;
  position: relative;
  padding-left: 28px;
  font-size: 0.85rem; /* Smaller font for list items */
  color: #ddd;
}

.price .box ul li i {
  color: var(--theme-color);
  position: absolute;
  left: 0;
  top: 3px;
}

/* === BUTTON === */
.price .box .btn {
  display: block;
  width: 100%;
  max-width: 180px; /* Slightly smaller button */
  margin: 0 auto;
  padding: 10px 0;
  background-color: var(--theme-color);
  color: white;
  border: none;
  border-radius: 25px;
  font-size: 0.9rem; /* Smaller font for button */
  font-weight: 600;
  text-decoration: none;
  transition: background-color 0.3s ease, transform 0.2s ease;
  cursor: pointer;
}

.price .box .btn:hover {
  transform: scale(1.05);
  background-color: var(--theme-color); /* tetap sama, bisa diganti lebih terang jika mau */
}

.price .box .btn:active {
  transform: scale(0.97);
}
    .lihat-semua-container {
      display: flex;
      justify-content: flex-end;
      margin-bottom: 20px;
      padding-right: 30px;
    }

    .lihat-semua-container button {
      background: none;
      border: none;
      color: #ffb547;
      font-weight: bold;
      font-size: 16px;
      cursor: pointer;
      transition: color 0.3s;
    }

    .lihat-semua-container button:hover {
      color: #fff;
    }

  </style>

    <!-- JUDUL -->
  <h1 class="heading">OUR <span>PRICING</span></h1><!-- Tombol Lihat Semua -->
  <div class="lihat-semua-container">
    <form action="/senyawa-wo/sections/price.php" method="POST">
      <button type="submit">Lihat Semua</button>
    </form>
  </div>

  <!-- Daftar Paket -->
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
    }

    $manual_packages = [
      ["Akad Nikah & Beda Hari", "Rp 500.000", []],
      ["Engagement Regular", "Rp 1.250.000", [
        "Crew 2 orang (PIC Event, Show Director)",
        "Konsultasi Konsep",
        "Rundown Acara",
        "Games",
        "Free Bouqet bunga",
        "Free MC"
      ]],
      ["Engagement VIP", "Rp 1.850.000", [
        "Crew 4 orang (PIC Event, Show Director, Cheeker, PIC Media)",
        "Konsultasi Konsep",
        "Rundown Acara",
        "Games",
        "Free Bouqet bunga",
        "Free Undangan Digital",
        "Free Video Moment",
        "Free MC"
      ]],
      ["Akad Nikah Reguler", "Rp 2.250.000", [
        "Crew 4 orang (PIC Event, Show Director, Cheeker, PIC Tamu)",
        "Konsultasi Konsep",
        "Rundown Acara",
        "Teks Sungkeman",
        "Teks Mohon Restu",
        "Ten Card",
        "Short Video Moment",
        "Free MC"
      ]],
    ];

    $use_manual_data = true;
    if (isset($conn) && $conn instanceof mysqli && !$conn->connect_error) {
      $result = $conn->query("SELECT * FROM packages ORDER BY price ASC LIMIT 4");
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
          echo '<form action="pages/chekout.php" method="POST">';
          echo '<input type="hidden" name="package_id" value="' . $package['id'] . '">';
          echo '<button type="submit" class="btn">Check Out</button>';
          echo '</form>';
          echo '</div>';
        }
      }
    }

    if ($use_manual_data) {
      foreach ($manual_packages as $pkg) {
        echo '<div class="box">';
        echo '<h3 class="tittle">' . $pkg[0] . '</h3>';
        echo '<h3 class="amount">' . $pkg[1] . '</h3>';
        echo '<ul>';
        foreach ($pkg[2] as $item) {
          echo '<li><i class="fas fa-check"></i> ' . $item . '</li>';
        }
        echo '</ul>';
        echo '<form action="../pages/select_package.php" method="GET">';
        echo '<input type="hidden" name="package_name" value="' . htmlspecialchars($pkg[0]) . '">';
        echo '<input type="hidden" name="package_price" value="' . htmlspecialchars($pkg[1]) . '">';
        echo '<button type="submit" class="btn">Check Out</button>';
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
