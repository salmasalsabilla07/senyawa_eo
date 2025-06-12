<?php
// api_dashboard_data.php
header('Content-Type: application/json');

include('config.php'); // koneksi database, sesuaikan pathnya

// Query hitung total pemesanan
$qTotal = mysqli_query($conn, "SELECT COUNT(*) as total FROM pemesanan");
$totalOrders = mysqli_fetch_assoc($qTotal)['total'] ?? 0;

// Query hitung pembayaran pending
$qPending = mysqli_query($conn, "SELECT COUNT(*) as pending FROM pembayaran WHERE status = 'pending'");
$totalPending = mysqli_fetch_assoc($qPending)['pending'] ?? 0;

// Query hitung event hari ini
$today = date("Y-m-d");
$qToday = mysqli_query($conn, "SELECT COUNT(*) as jumlah FROM jadwal WHERE tanggal_event = '$today'");
$totalEventsToday = mysqli_fetch_assoc($qToday)['jumlah'] ?? 0;

echo json_encode([
    'totalOrders' => $totalOrders,
    'totalPending' => $totalPending,
    'totalEventsToday' => $totalEventsToday
]);
