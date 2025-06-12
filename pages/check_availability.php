<?php
include 'config.php';

$package_id = $_POST['package_id'] ?? null;
$tanggal_event = $_POST['booking_date'] ?? null;  

if (!$package_id || !$tanggal_event) {
    die("Data paket dan tanggal harus dipilih.");
}

$query = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE package_id = ? AND tanggal_event = ?");
if (!$query) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}

$query->bind_param("is", $package_id, $tanggal_event);
if (!$query->execute()) {
    die("Execute failed: (" . $query->errno . ") " . $query->error);
}

$query->bind_result($count);
$query->fetch();
$query->close();

if ($count > 0) {
    echo "Maaf, tanggal tersebut sudah penuh. Silakan pilih tanggal lain.";
} else {
    header("Location: booking_form.php?package_id=$package_id&tanggal_event=$tanggal_event");
    exit();
}
?>
