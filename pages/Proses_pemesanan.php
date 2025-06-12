<?php
include 'config.php';

$package_id = $_POST['package_id'] ?? null;
$tanggal_event = $_POST['tanggal_event'] ?? null;
$nama = $_POST['nama_lengkap'] ?? '';
$email = $_POST['email'] ?? '';
$telepon = $_POST['nomor_telepon'] ?? '';

if (!$package_id || !$tanggal_event || !$nama || !$email || !$telepon) {
    die("Data tidak lengkap, mohon isi semua form.");
}

$package_id = (int)$package_id;
$tanggal_event = mysqli_real_escape_string($conn, $tanggal_event);
$nama = mysqli_real_escape_string($conn, $nama);
$email = mysqli_real_escape_string($conn, $email);
$telepon = mysqli_real_escape_string($conn, $telepon);

// Cek apakah tanggal sudah dibooking
$query_check = "SELECT COUNT(*) AS jumlah FROM bookings WHERE package_id = $package_id AND tanggal_event = '$tanggal_event'";
$result_check = mysqli_query($conn, $query_check);
$row = mysqli_fetch_assoc($result_check);

if ($row['jumlah'] > 0) {
    die("Maaf, tanggal yang Anda pilih sudah dipesan. Silakan pilih tanggal lain.");
}

// Simpan booking
$query_insert = "INSERT INTO bookings (package_id, tanggal_event, nama_lengkap, email, nomor_telepon, status) 
                 VALUES ($package_id, '$tanggal_event', '$nama', '$email', '$telepon', 'pending')";
if (mysqli_query($conn, $query_insert)) {
    $booking_id = mysqli_insert_id($conn);
    header("Location: pembayaran.php?booking_id=$booking_id");
    exit;
} else {
    die("Gagal menyimpan data: " . mysqli_error($conn));
}
