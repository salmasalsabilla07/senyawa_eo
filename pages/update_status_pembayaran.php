<?php
include 'config.php';

$booking_id = $_POST['booking_id'] ?? null;
$status = $_POST['status'] ?? '';

if (!$booking_id || !is_numeric($booking_id) || !in_array($status, ['paid', 'cancelled'])) {
    die("Data tidak valid.");
}
$booking_id = (int)$booking_id;
$status = mysqli_real_escape_string($conn, $status);

$query = "UPDATE bookings SET status = '$status' WHERE id = $booking_id";
if (mysqli_query($conn, $query)) {
    echo "Status pembayaran berhasil diupdate.<br>";
    echo "<a href='admin_dashboard.php'>Kembali ke Dashboard Admin</a>";
} else {
    die("Gagal update status: " . mysqli_error($conn));
}
