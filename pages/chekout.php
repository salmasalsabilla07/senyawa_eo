<?php
include 'config.php';

if (!isset($_POST['package_id'])) {
    die("Paket tidak dipilih.");
}

$package_id = intval($_POST['package_id']);

$stmt = $conn->prepare("SELECT * FROM packages WHERE id = ?");
$stmt->bind_param("i", $package_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Paket tidak ditemukan.");
}

$package = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Checkout - <?= htmlspecialchars($package['name']) ?></title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #222;
      color: #d6a84f;
      margin: 0; padding: 0;
      min-height: 100vh;
    }
    .wrapper {
      display: flex; justify-content: center; align-items: flex-start;
      min-height: 100vh; padding: 40px 20px;
    }
    .checkout-box {
      max-width: 600px; width: 100%;
    }
    h2 {
      font-weight: 700;
      color: #d6a84f;
      margin-bottom: 10px;
      text-shadow: 1px 1px 2px #000000aa;
    }
    p {
      font-size: 18px;
      color: #fff1d0;
      margin-bottom: 25px;
      text-shadow: 1px 1px 1px #000000bb;
    }
    label {
      display: block;
      margin: 12px 0 6px 0;
      font-weight: 600;
      color: #d6a84f;
      text-shadow: 1px 1px 2px #000000cc;
    }

    .form-group {
  display: flex;
  flex-direction: column;
  align-items: center; /* agar label dan select di tengah */
  margin-bottom: 20px;
}

select {
  width: 170px;
  padding: 10px;
  font-size: 16px;
  border-radius: 6px;
  border: none;
  background-color: #333;
  color: #d6a84f;
  box-shadow: 0 2px 5px rgba(0,0,0,0.5);
  transition: background-color 0.3s ease;
  margin-right: 12px;  /* <-- tambahkan ini */
}

    select:hover, select:focus {
      background-color: #333;
      outline: none;
    }
    .date-list {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
      gap: 12px;
      margin-top: 20px;
    }
    .date-item {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 12px 18px;
      background-color: #333;
      border-radius: 10px;
      cursor: pointer;
      font-weight: 600;
      color: #d6a84f;
      box-shadow: 0 3px 6px rgba(0,0,0,0.4);
      user-select: none;
      transition: background-color 0.3s ease, color 0.3s ease;
      text-align: center;
    }
    .date-item:hover {
      background-color: #d6a84f;
      color: #222;
      box-shadow: 0 5px 12px rgba(243,179,67,0.7);
    }
    .date-item.selected {
      background-color: #d6a84f;
      color: #222;
      box-shadow: 0 0 12px 2px #d18a05aa;
    }
    .date-item.booked {
      background-color: #5a1e1e;
      color: #ff9999;
      cursor: not-allowed;
      text-decoration: line-through;
      box-shadow: none;
      pointer-events: none;
    }
    .btn {
      margin-top: 30px;
      padding: 14px 28px;
      background-color: #d6a84f;
      color: #333;
      border: none;
      font-size: 18px;
      font-weight: 700;
      border-radius: 8px;
      cursor: pointer;
      box-shadow: 0 6px 15px rgba(243,179,67,0.7);
      transition: background-color 0.3s ease, color 0.3s ease;
    }
    .btn:hover:not([disabled]) {
      background-color: #d6a84f;
      color: #fff;
      box-shadow: 0 8px 18px #d18a05dd;
    }
    .btn[disabled] {
      background-color: #333;
      color: #bfbfbf;
      cursor: not-allowed;
      box-shadow: none;
    }
  </style>
</head>
<body>

<div class="wrapper">
  <div class="checkout-box">
    <h2>Package: <?= htmlspecialchars($package['name']) ?></h2>
    <p>Price: Rp<?= number_format($package['price'], 0, ',', '.') ?></p>

    <form id="bookingForm" action="check_availability.php" method="post">
      <input type="hidden" name="package_id" value="<?= $package['id'] ?>">
      <input type="hidden" name="booking_date" id="selectedDate" required>

      <label for="year">Select Year:</label>
      <select id="year" required onchange="loadDates()">
        <option value="">-- Select Year --</option>
        <?php
        $currentYear = date('Y');
        for ($y = $currentYear; $y <= $currentYear + 2; $y++) {
            echo "<option value=\"$y\">$y</option>";
        }
        ?>
      </select>

      <label for="month">Select Month:</label>
      <select id="month" required onchange="loadDates()">
        <option value="">-- Select Month --</option>
        <?php
        $bulanIndo = [
            1 => 'January', 2 => 'February', 3 => ' March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];
        foreach ($bulanIndo as $num => $name) {
            echo "<option value=\"$num\">$name</option>";
        }
        ?>
      </select>

      <div class="date-list" id="dateList"></div>

      <button type="submit" class="btn" disabled id="submitBtn">Continue</button>
    </form>
  </div>
</div>

<script>
  let selectedDate = null;

  function loadDates() {
    selectedDate = null;
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('selectedDate').value = '';

    const packageId = <?= json_encode($package['id']) ?>;
    const year = document.getElementById('year').value;
    const month = document.getElementById('month').value;
    const dateList = document.getElementById('dateList');

    dateList.innerHTML = '';

    if (!year || !month) return;

    dateList.innerHTML = '<p style="color:#f3b343;">Mengambil data...</p>';

    fetch(`get_available_dates.php?package_id=${packageId}&month=${month}&year=${year}`)
      .then(res => res.json())
      .then(data => {
        dateList.innerHTML = '';

        if (!Array.isArray(data) || data.length === 0) {
          dateList.innerHTML = '<p style="color:#f3b343;">Tidak ada tanggal tersedia di bulan ini.</p>';
          return;
        }

        const today = new Date();
        today.setHours(0, 0, 0, 0);

        data.forEach(item => {
          const div = document.createElement('div');
          div.className = 'date-item';
          div.textContent = item.date;

          const itemDate = new Date(item.date);
          itemDate.setHours(0, 0, 0, 0);

          if (!item.available || itemDate < today) {
            div.classList.add('booked');
          } else {
            div.addEventListener('click', () => {
              document.querySelectorAll('.date-item.selected').forEach(el => el.classList.remove('selected'));
              div.classList.add('selected');
              selectedDate = item.date;
              document.getElementById('selectedDate').value = selectedDate;
              document.getElementById('submitBtn').disabled = false;
            });
          }

          dateList.appendChild(div);
        });
      })
      .catch(err => {
        dateList.innerHTML = '<p style="color:#f3b343;">Gagal mengambil data tanggal.</p>';
        console.error(err);
      });
  }
</script>

</body>
</html>
