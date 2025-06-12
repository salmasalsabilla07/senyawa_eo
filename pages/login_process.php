<?php
session_start();
include('config.php');

$username = $_POST['username'];
$password = $_POST['password'];

// Query untuk mencari user berdasarkan username
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);


if ($user) {
    // 1. Coba verifikasi dengan password_hash (untuk user yang daftar dari register)
    if (password_verify($password, $user['password'])) {
        $_SESSION['user'] = $username;
        $_SESSION['role'] = $user['role'];
    }
    // 2. Jika gagal, coba verifikasi langsung (untuk admin yang dibuat manual di DB)
    elseif ($password === $user['password']) {
        $_SESSION['user'] = $username;
        $_SESSION['role'] = $user['role'];
    }

    // Jika sudah login (password cocok)
    if (isset($_SESSION['user'])) {
        if ($user['role'] == 'admin') {
            header("Location: dashboard.php");
        } else {
            header("Location: ../index.php");
        }
        exit();
    }
}

// Jika gagal login
echo "<script>alert('Username atau Password salah'); window.location='login.php';</script>";

mysqli_stmt_close($stmt);
