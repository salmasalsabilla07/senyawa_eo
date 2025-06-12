<?php
session_start();

if (!isset($_SESSION['order_id'])) {
    die("Akses tidak sah.");
}

$order_id = $_SESSION['order_id'];
$total_harga = $_SESSION['total_harga'];
$package_id = $_SESSION['package_id'];
$tanggal_event = $_SESSION['tanggal_event'];
$nama = $_SESSION['nama_lengkap'];
$email = $_SESSION['email'];
$nomor_telepon = $_SESSION['nomor_telepon'];

// Koneksi database
$conn = new mysqli("localhost", "root", "", "senyawa_wo");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data pengantin dari tabel brides
$stmt = $conn->prepare("SELECT 
    nama_pria, panggilan_pria, alamat_pria, ayah_pria, ibu_pria,
    nama_wanita, panggilan_wanita, alamat_wanita, ayah_wanita, ibu_wanita,
    tanggal_event, jam_event, lokasi_event
    FROM brides WHERE order_id = ?");
$stmt->bind_param("s", $order_id);
$stmt->execute();
$stmt->bind_result(
    $nama_pria,
    $panggilan_pria,
    $alamat_pria,
    $ayah_pria,
    $ibu_pria,
    $nama_wanita,
    $panggilan_wanita,
    $alamat_wanita,
    $ayah_wanita,
    $ibu_wanita,
    $tanggal_event_db,
    $jam_event,
    $lokasi_event
);
$stmt->fetch();
$stmt->close();

if (!$nama_pria) {
    die("Data pengantin belum lengkap, silakan isi form pengantin terlebih dahulu.");
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Order Details & Review</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #111;
            color: #d6a84f;
            padding: 50px;
            text-align: center;
        }

        .payment-box {
            background-color: #222;
            padding: 30px;
            max-width: 700px;
            margin: auto;
            border-radius: 15px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.3);
            text-align: left;
        }

        h2 {
            text-align: center;
            color: #d6a84f;
            margin-bottom: 25px;
        }

        .info-group {
            margin-bottom: 25px;
        }

        .info-group h3 {
            color: #d6a84f;
            border-bottom: 1px solid #444;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .info-group p {
            margin: 5px 0;
            font-size: 16px;
            line-height: 1.4;
        }

        .info-group p strong {
            color: #fff;
            width: 160px;
            display: inline-block;
        }

        .btn {
            display: block;
            width: 100%;
            margin-top: 30px;
            background-color: #d6a84f;
            color: #111;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
            text-decoration: none;
            text-align: center;
        }

        .btn:hover {
            background-color: #c1a040;
        }
    </style>
</head>

<body>
    <div class="payment-box">
        <h2>Review Order Details</h2>

        <div class="info-group">
            <h3>Customer Info</h3>
            <p><strong>Name:</strong> <?= htmlspecialchars($nama) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($nomor_telepon) ?></p>
            <p><strong>Event Date:</strong> <?= htmlspecialchars($tanggal_event_db ?? $tanggal_event) ?></p>
            <p><strong>Total Payment:</strong> Rp<?= number_format($total_harga, 0, ',', '.') ?></p>
        </div>

        <div class="info-group">
            <h3>Groom Data</h3>
            <p><strong>Full name:</strong> <?= htmlspecialchars($nama_pria) ?></p>
            <p><strong>Nickname:</strong> <?= htmlspecialchars($panggilan_pria) ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($alamat_pria) ?></p>
            <p><strong>Father's name:</strong> <?= htmlspecialchars($ayah_pria) ?></p>
            <p><strong>Mother's Name:</strong> <?= htmlspecialchars($ibu_pria) ?></p>
        </div>

        <div class="info-group">
            <h3>Bride's Data</h3>
            <p><strong>Full name:</strong> <?= htmlspecialchars($nama_wanita) ?></p>
            <p><strong>Nickname:</strong> <?= htmlspecialchars($panggilan_wanita) ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($alamat_wanita) ?></p>
            <p><strong>Father's name:</strong> <?= htmlspecialchars($ayah_wanita) ?></p>
            <p><strong>Mother's Name:</strong> <?= htmlspecialchars($ibu_wanita) ?></p>
        </div>

        <div class="info-group">
            <h3>Reception Details</h3>
            <p><strong>Date:</strong> <?= htmlspecialchars($tanggal_event_db) ?></p>
            <p><strong>Time:</strong> <?= htmlspecialchars($jam_event) ?></p>
            <p><strong>Location:</strong> <?= htmlspecialchars($lokasi_event) ?></p>
        </div>

        <!-- Tombol diarahkan ke halaman baru untuk transfer manual -->
        <a href="choise_payment.php" class="btn">Continue to Payment</a>
    </div>
</body>

</html>