<?php
session_start();
$conn = new mysqli("localhost", "root", "", "senyawa_wo");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$order_id = $_GET['order_id'] ?? $_SESSION['order_id'] ?? '';
if (!$order_id) {
    die("Order ID tidak ditemukan.");
}

// Ambil tanggal_event otomatis dari tabel bookings
$tanggal_event = '';
$stmt = $conn->prepare("SELECT tanggal_event FROM bookings WHERE order_id = ?");
if (!$stmt) {
    die("Prepare statement gagal: " . $conn->error);
}
$stmt->bind_param("s", $order_id);
$stmt->execute();
$stmt->bind_result($tanggal_event);
$stmt->fetch();
$stmt->close();

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $panggilan_pria = $_POST['panggilan_pria'];
    $alamat_pria = $_POST['alamat_pria'];
    $nama_pria = $_POST['nama_pria'];
    $ayah_pria = $_POST['ayah_pria'];
    $ibu_pria = $_POST['ibu_pria'];

    $panggilan_wanita = $_POST['panggilan_wanita'];
    $alamat_wanita = $_POST['alamat_wanita'];
    $nama_wanita = $_POST['nama_wanita'];
    $ayah_wanita = $_POST['ayah_wanita'];
    $ibu_wanita = $_POST['ibu_wanita'];

    $jam_event = $_POST['jam_event'];
    $lokasi_event = $_POST['lokasi_event'];

    $stmt = $conn->prepare("INSERT INTO brides (
        order_id, nama_pria, panggilan_pria, alamat_pria, ayah_pria, ibu_pria,
        nama_wanita, panggilan_wanita, alamat_wanita, ayah_wanita, ibu_wanita,
        tanggal_event, jam_event, lokasi_event
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE 
        nama_pria=?, panggilan_pria=?, alamat_pria=?, ayah_pria=?, ibu_pria=?,
        nama_wanita=?, panggilan_wanita=?, alamat_wanita=?, ayah_wanita=?, ibu_wanita=?,
        tanggal_event=?, jam_event=?, lokasi_event=?");

    if (!$stmt) {
        die("Prepare statement gagal: " . $conn->error);
    }

    $stmt->bind_param(
        "sssssssssssssssssssssssssss",
        $order_id, $nama_pria, $panggilan_pria, $alamat_pria, $ayah_pria, $ibu_pria,
        $nama_wanita, $panggilan_wanita, $alamat_wanita, $ayah_wanita, $ibu_wanita,
        $tanggal_event, $jam_event, $lokasi_event,
        $nama_pria, $panggilan_pria, $alamat_pria, $ayah_pria, $ibu_pria,
        $nama_wanita, $panggilan_wanita, $alamat_wanita, $ayah_wanita, $ibu_wanita,
        $tanggal_event, $jam_event, $lokasi_event
    );

    if ($stmt->execute()) {
        header("Location: metode_pembayaran.php?order_id=$order_id");
        exit;
    } else {
        echo "Gagal menyimpan data pengantin: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Form Data Pengantin Lengkap</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #111;
            color: #d6a84f;
            padding: 40px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #d6a84f;
        }

        form {
            background: #222;
            padding: 30px;
            max-width: 700px;
            margin: auto;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.4);
        }

        label {
            display: block;
            margin-top: 10px;
            margin-bottom: 5px;
            font-weight: bold;
            color: #d6a84f;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 10px;
            border: 1px solid #555;
            background-color: #222;
            color: #d6a84f;
            font-size: 16px;
        }

        input::placeholder {
            color: #bbb;
        }

        .btn {
            background-color: #d6a84f;
            color: #222;
            padding: 12px 20px;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s ease;
            margin-top: 20px;
        }

        .btn:hover {
            background-color: #c1a040;
        }

        .section-title {
            margin-top: 25px;
            margin-bottom: 10px;
            font-size: 18px;
            color: #fff;
            border-bottom: 1px solid #444;
            padding-bottom: 5px;
        }
    </style>
</head>
<body>

<h2>Form Data Pengantin</h2>

<form method="POST">

    <div class="section-title">Groom Data</div>
    <label>Full name</label>
    <input type="text" name="nama_pria" required>
    <label>Nickname</label>
    <input type="text" name="panggilan_pria" required>
    <label>Address</label>
    <input type="text" name="alamat_pria" required>
    <label>Father's name</label>
    <input type="text" name="ayah_pria" required>
    <label>Mother's Name</label>
    <input type="text" name="ibu_pria" required>

    <div class="section-title">Bride's Data</div>
    <label>Full name</label>
    <input type="text" name="nama_wanita" required>
    <label>Nickname</label>
    <input type="text" name="panggilan_wanita" required>
    <label>Address</label>
    <input type="text" name="alamat_wanita" required>
    <label>Father's name</label>
    <input type="text" name="ayah_wanita" required>
    <label>Mother's Name</label>
    <input type="text" name="ibu_wanita" required>

    <div class="section-title">Event Details</div>
    <label>Event Date</label>
    <input type="text" name="tanggal_event" value="<?= htmlspecialchars($tanggal_event) ?>" readonly>
    <label>Event Time</label>
    <input type="text" name="jam_event" placeholder="Example: 10:00 AM" required>
    <label>Event Location</label>
    <input type="text" name="lokasi_event" required>

    <button type="submit" class="btn">Save and Continue</button>
</form>

</body>
</html>
