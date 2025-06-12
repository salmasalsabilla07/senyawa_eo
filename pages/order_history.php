<?php
session_start();
include 'config.php'; 

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Query join bookings + packages
$query = "SELECT 
            b.id AS order_id,
            b.tanggal_event AS order_date,
            p.nama_paket AS package_name,
            b.status_pembayaran AS status,
            (p.harga * b.jumlah) AS total_price
          FROM bookings b
          JOIN packages p ON b.package_id = p.id
          WHERE b.user_id = ?
          ORDER BY b.tanggal_event DESC";

$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Riwayat Pemesanan - Senyawa EO</title>
  <style>
    body {
      background-color: #121212;
      color: #eee;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 20px;
    }

    h2 {
      color: #f39c12;
      text-align: center;
      margin-bottom: 30px;
      font-weight: 700;
      letter-spacing: 1.5px;
    }

    table {
      width: 90%;
      max-width: 900px;
      margin: 0 auto 40px auto;
      border-collapse: collapse;
      box-shadow: 0 0 15px rgba(243, 156, 18, 0.3);
      background-color: #222;
      border-radius: 10px;
      overflow: hidden;
    }

    thead tr {
      background-color: #f39c12;
      color: #121212;
      text-transform: uppercase;
      letter-spacing: 1px;
      font-weight: 600;
    }

    thead th, tbody td {
      padding: 15px 20px;
      text-align: center;
      border-bottom: 1px solid #333;
    }

    tbody tr:hover {
      background-color: #333;
      cursor: default;
      transition: background-color 0.3s ease;
    }

    tbody tr:last-child td {
      border-bottom: none;
    }

    p {
      text-align: center;
      font-size: 1.2rem;
      margin-top: 50px;
      color: #f39c12;
      font-weight: 600;
    }

    @media (max-width: 600px) {
      table, thead, tbody, th, td, tr {
        display: block;
      }

      thead tr {
        position: absolute;
        top: -9999px;
        left: -9999px;
      }

      tbody tr {
        margin-bottom: 20px;
        background-color: #222;
        border-radius: 10px;
        padding: 10px;
        box-shadow: 0 0 10px rgba(243, 156, 18, 0.2);
      }

      tbody td {
        border: none;
        position: relative;
        padding-left: 50%;
        text-align: left;
      }

      tbody td::before {
        position: absolute;
        top: 50%;
        left: 20px;
        transform: translateY(-50%);
        font-weight: 700;
        color: #f39c12;
        white-space: nowrap;
      }

      tbody td:nth-of-type(1)::before { content: "ID Pesanan"; }
      tbody td:nth-of-type(2)::before { content: "Tanggal Event"; }
      tbody td:nth-of-type(3)::before { content: "Nama Paket"; }
      tbody td:nth-of-type(4)::before { content: "Status"; }
      tbody td:nth-of-type(5)::before { content: "Total Harga"; }
    }
  </style>
</head>
<body>

<h2>Riwayat Pemesanan Anda</h2>

<?php if ($result->num_rows > 0): ?>
  <table>
    <thead>
      <tr>
        <th>ID Pesanan</th>
        <th>Tanggal Event</th>
        <th>Nama Paket</th>
        <th>Status</th>
        <th>Total Harga</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['order_id']) ?></td>
        <td><?= htmlspecialchars(date('d M Y', strtotime($row['order_date']))) ?></td>
        <td><?= htmlspecialchars($row['package_name']) ?></td>
        <td><?= htmlspecialchars(ucfirst($row['status'])) ?></td>
        <td>Rp <?= number_format($row['total_price'], 0, ',', '.') ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
<?php else: ?>
  <p>Belum ada riwayat pemesanan.</p>
<?php endif; ?>

</body>
</html>
