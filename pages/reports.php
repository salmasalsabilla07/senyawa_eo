<?php
session_start();
include('config.php');

if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Ambil total pemesanan dengan progress_status 'done' dan pembayaran berhasil
$total_orders_query = "SELECT COUNT(*) AS total FROM bookings WHERE progress_status = 'done' AND status_pembayaran = 'berhasil'";
$total_orders_result = mysqli_query($conn, $total_orders_query);
$total_orders = 0;
if ($total_orders_result) {
    $total_orders = mysqli_fetch_assoc($total_orders_result)['total'];
}

// Ambil total pemasukan dari booking yang sudah 'done' dan pembayaran berhasil
$total_income_query = "SELECT SUM(jumlah) AS total FROM bookings WHERE progress_status = 'done' AND status_pembayaran = 'berhasil'";
$total_income_result = mysqli_query($conn, $total_income_query);
$total_income = 0;
if ($total_income_result) {
    $total_income = mysqli_fetch_assoc($total_income_result)['total'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Order Completed Report</title>
    <style>
        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: #1e1e1e;
            color: #f0f0f0;
            font-size: 14px;
        }

        .wrapper {
            display: flex;
            width: 100%;
        }

        .main-content {
            flex: 1;
            padding: 30px 50px;
            background-color: #2b2b2b;
            overflow-y: auto;
        }

        h2 {
            color: #ffcd00;
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        a.back-link {
            display: inline-block;
            margin-bottom: 30px;
            color: #ffcd00;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        a.back-link:hover {
            color: #ffe600;
        }

        ul.report-list {
            list-style: none;
            padding: 0;
            max-width: 500px;
        }

        ul.report-list li {
            background-color: #3a3a3a;
            margin-bottom: 20px;
            padding: 20px 25px;
            border-radius: 12px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.4);
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #e0e0e0;
        }

        ul.report-list li strong {
            color: #ffcd00;
            font-size: 20px;
        }

        /* Scrollbar styling */
        .main-content::-webkit-scrollbar {
            width: 8px;
        }

        .main-content::-webkit-scrollbar-thumb {
            background-color: #555;
            border-radius: 10px;
        }

        .main-content::-webkit-scrollbar-track {
            background-color: #2b2b2b;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include_once 'sidebar_dashboard.php'; ?>

        <div class="main-content">
            <h2>Completed Order Activity Report</h2>
            <ul class="report-list">
                <li>Total Order Completed: <strong><?= $total_orders; ?></strong></li>
                <li>Total Income Completed: <strong>Rp<?= number_format($total_income, 0, ',', '.'); ?></strong></li>
            </ul>
        </div>
    </div>
</body>
</html>
