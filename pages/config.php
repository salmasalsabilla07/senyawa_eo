<?php
$host = 'localhost';
$user = 'root';  // Sesuaikan dengan username MySQL 
$pass = '';      // Sesuaikan dengan password MySQL 
$dbname = 'senyawa_wo';

// Koneksi ke database
$conn = mysqli_connect($host, $user, $pass, $dbname);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
