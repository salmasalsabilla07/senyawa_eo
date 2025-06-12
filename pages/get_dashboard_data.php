<?php
session_start();
include 'config.php';
header('Content-Type: application/json');

try {
    // Total Orders dengan status 'berhasil'
    $sqlTotalOrders = "SELECT COUNT(*) as total FROM bookings WHERE status_pembayaran = 'berhasil'";
    $resultTotal = $conn->query($sqlTotalOrders);
    if (!$resultTotal) throw new Exception("Query totalOrders error: " . $conn->error);
    $totalOrders = $resultTotal->fetch_assoc()['total'] ?? 0;

    // Tanggal hari ini
    $dateToday = date('d M Y');

    // Events hari ini
    $today = date('Y-m-d');
    $sqlEventsToday = "SELECT COUNT(*) as total FROM bookings WHERE tanggal_event = '$today'";
    $resultEventsToday = $conn->query($sqlEventsToday);
    if (!$resultEventsToday) throw new Exception("Query eventsToday error: " . $conn->error);
    $totalEventsToday = $resultEventsToday->fetch_assoc()['total'] ?? 0;

    // Events besok
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    $sqlEventsTomorrow = "SELECT COUNT(*) as total FROM bookings WHERE tanggal_event = '$tomorrow'";
    $resultEventsTomorrow = $conn->query($sqlEventsTomorrow);
    if (!$resultEventsTomorrow) throw new Exception("Query eventsTomorrow error: " . $conn->error);
    $totalEventsTomorrow = $resultEventsTomorrow->fetch_assoc()['total'] ?? 0;

    // Pesanan hari ini
    $sqlRecentOrders = "
        SELECT b.nama_lengkap, b.tanggal_event, p.name AS nama_paket, b.status_pembayaran
        FROM bookings b
        JOIN packages p ON b.package_id = p.id
        WHERE DATE(b.created_at) = CURDATE()
        ORDER BY b.created_at DESC
        LIMIT 10
    ";
    $resultRecent = $conn->query($sqlRecentOrders);
    if (!$resultRecent) throw new Exception("Query recentOrders error: " . $conn->error);
    $recentOrders = [];
    while ($row = $resultRecent->fetch_assoc()) {
        $recentOrders[] = $row;
    }

    echo json_encode([
        'totalOrders' => (int)$totalOrders,
        'dateToday' => $dateToday,
        'totalEventsToday' => (int)$totalEventsToday,
        'totalEventsTomorrow' => (int)$totalEventsTomorrow,
        'recentOrders' => $recentOrders
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

$conn->close();
