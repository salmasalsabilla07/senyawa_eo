<?php
session_start();
include('config.php'); 

// Cek login dan role admin
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Ambil id user dari parameter GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("User ID tidak ditemukan.");
}

$user_id = intval($_GET['id']);

// Cek user ada di database
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    die("User ID tidak ditemukan.");
}

// Delete user
$delete_query = "DELETE FROM users WHERE id = $user_id";
if (mysqli_query($conn, $delete_query)) {
    header("Location: user_list.php?message=User berhasil dihapus");
    exit;
} else {
    die("Gagal menghapus user: " . mysqli_error($conn));
}
?>
