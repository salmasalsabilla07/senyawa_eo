<?php
session_start();        // Mulai session
session_destroy();      // Hapus semua session (user, role, dll)
header("Location: login.php");  // Balik ke halaman utama
exit();
?>
