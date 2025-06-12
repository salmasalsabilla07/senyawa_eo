<?php
include 'config.php';

header('Content-Type: application/json');

if (!isset($_GET['package_id'], $_GET['month'], $_GET['year'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Parameter tidak lengkap']);
    exit;
}

$package_id = (int)$_GET['package_id'];
$month = (int)$_GET['month'];
$year = (int)$_GET['year'];

if ($month < 1 || $month > 12 || $year < 2023 || $year > (int)date('Y') + 5) {
    http_response_code(400);
    echo json_encode(['error' => 'Bulan atau tahun tidak valid']);
    exit;
}

// Query tanggal booked
$query = "
    SELECT DISTINCT DATE(tanggal_event) AS booked_date
    FROM bookings
    WHERE package_id = ? 
      AND MONTH(tanggal_event) = ? 
      AND YEAR(tanggal_event) = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $package_id, $month, $year);
$stmt->execute();
$result = $stmt->get_result();

$bookedDates = [];
while ($row = $result->fetch_assoc()) {
    $bookedDates[] = (new DateTime($row['booked_date']))->format('Y-m-d');
    // Pastikan format YYYY-MM-DD
    $bookedDates[] = $row['booked_date'];
}

// Generate seluruh tanggal bulan
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$response = [];

for ($day = 1; $day <= $daysInMonth; $day++) {
    $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
    // Periksa apakah tanggal termasuk booked
    $isBooked = in_array($dateStr, $bookedDates);
    $response[] = [
        'date' => $dateStr,
        'available' => !$isBooked
    ];
}

echo json_encode($response);
?>
