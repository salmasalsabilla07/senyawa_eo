<?php
session_start();
include('config.php'); // Pastikan path-nya benar

$username = $_POST['username'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$role = 'user'; // Default role

// Validasi konfirmasi password
if ($password !== $confirm_password) {
    echo "<script>alert('Konfirmasi password tidak cocok!'); window.location='register.php';</script>";
    exit;
}
// Cek apakah username sudah digunakan
$stmt = mysqli_prepare($conn, "SELECT username FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    echo "<script>alert('Username sudah digunakan! Silakan pilih username lain.'); window.location='register.php';</script>";
    mysqli_stmt_close($stmt);
    exit;
}
mysqli_stmt_close($stmt);

// Hash password sebelum disimpan
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Simpan user ke database
$stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt, "sss", $username, $hashed_password, $role);

if (mysqli_stmt_execute($stmt)) {
    echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='login.php';</script>";
} else {
    echo "<script>alert('Registrasi gagal: " . mysqli_error($conn) . "'); window.location='register.php';</script>";
}

mysqli_stmt_close($stmt);
