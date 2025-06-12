<?php
session_start();

$nama = $_SESSION['nama_lengkap'] ?? 'Pelanggan';
$order_id = $_SESSION['order_id'] ?? 'N/A';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Upload Berhasil</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #111;
            color: #fff;
            padding: 50px;
            text-align: center;
        }

        .box {
            background-color: #222;
            max-width: 600px;
            margin: auto;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.6);
        }

        h2 {
            color: #d6a84f;
            margin-bottom: 20px;
        }

        p {
            line-height: 1.8;
        }

        .btn {
            margin-top: 30px;
            padding: 12px 25px;
            background-color: #d6a84f;
            color: #111;
            border: none;
            font-weight: bold;
            font-size: 16px;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            background-color: #c19a3a;
        }

        .btn-wa {
            background-color: #25D366;
            color: white;
            margin-left: 10px;
        }

        .btn-wa:hover {
            background-color: #1ebe5d;
        }
    </style>
</head>

<body>
    <div class="box">
        <h2>Thank you, <?= htmlspecialchars($nama) ?>!</h2>
        <p>Your Order ID: <strong><?= htmlspecialchars($order_id) ?></strong></p>
        <p>Your payment proof has been successfully uploaded.</p>
        <p>Our team will verify your payment shortly.</p>
        <p>Please wait for confirmation from the admin soon.</p>

        <a href="../index.php" class="btn">Back to Home</a>
        <a href="https://wa.me/6282282526275?text=Hello,%20I%20would%20like%20to%20confirm%20my%20payment%20with%20order%20number%20<?= urlencode($order_id) ?>" class="btn btn-wa" target="_blank">
            Contact Admin via WhatsApp
        </a>
    </div>


    </div>
</body>

</html>