<?php
session_start();
include 'config.php';

$order_id = $_SESSION['order_id'] ?? '';
$total_harga = $_SESSION['total_harga'] ?? 0;
$nama = $_SESSION['nama_lengkap'] ?? '';
$tanggal_event = $_SESSION['tanggal_event'] ?? '';


$dp_amount = $total_harga * 0.5;


$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_type = $_POST['payment_type'] ?? '';
    $bank = $_POST['bank'] ?? '';

    if (empty($payment_type) || empty($bank)) {
        $message = "Pilih metode pembayaran dan tujuan transfer terlebih dahulu.";
    } elseif (!isset($_FILES['bukti'])) {
        $message = "File bukti pembayaran harus diupload.";
    } else {
        $file = $_FILES['bukti'];

        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        if (!in_array($file['type'], $allowed_types)) {
            $message = "Format file tidak diizinkan. Gunakan JPG, PNG, atau PDF.";
        } elseif ($file['error'] !== 0) {
            $message = "Terjadi kesalahan saat upload file.";
        } else {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_file_name = 'bukti_' . $order_id . '_' . time() . '.' . $ext;
            $upload_dir = __DIR__ . '/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $upload_path = $upload_dir . $new_file_name;

            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                // Simpan langsung ke bookings
                $update = $conn->prepare("UPDATE bookings SET payment_type = ?, bank = ?, bukti_pembayaran = ?, uploaded_at = NOW() WHERE order_id = ?");
                $update->bind_param("ssss", $payment_type, $bank, $new_file_name, $order_id);
                if ($update->execute()) {
                    header('Location: upload_sukses.php');
                    exit;
                } else {
                    $message = "Gagal menyimpan data ke database.";
                    unlink($upload_path); // Hapus file jika gagal
                }
                $update->close();
            } else {
                $message = "Gagal memindahkan file ke server.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Metode Pembayaran</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #111;
            color: #fff;
            padding: 30px;
        }

        .container {
            max-width: 700px;
            margin: auto;
            background-color: #222;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.5);
        }

        h2 {
            text-align: center;
            color: #d6a84f;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 18px;
            margin-bottom: 10px;
            color: #d6a84f;
            border-bottom: 1px solid #444;
            padding-bottom: 5px;
        }

        .radio-group {
            margin: 15px 0;
        }

        .radio-option {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            background-color: #333;
            padding: 10px;
            border-radius: 10px;
            cursor: pointer;
        }

        .radio-option input {
            margin-right: 15px;
            transform: scale(1.2);
        }

        .radio-option img {
            width: 40px;
            margin-right: 15px;
        }

        .instruction {
            background-color: #1c1c1c;
            padding: 15px;
            margin-top: 20px;
            border-radius: 10px;
            display: none;
        }

        .btn {
            background-color: #d6a84f;
            border: none;
            padding: 12px 20px;
            color: #111;
            font-size: 16px;
            font-weight: bold;
            border-radius: 10px;
            cursor: pointer;
            width: 100%;
            margin-top: 30px;
        }

        .btn:hover {
            background-color: #c19a3a;
        }

        .highlight {
            color: #d6a84f;
        }

        input[type=file] {
            margin-top: 10px;
            background-color: #333;
            padding: 10px;
            border-radius: 10px;
            width: 100%;
            color: #fff;
        }

        .message {
            background-color: #d64f4f;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 20px;
            color: white;
            font-weight: bold;
        }

        .success {
            background-color: #4fd66d;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Select Payment Method</h2>

        <?php if ($message): ?>
            <div class="message <?= strpos($message, 'berhasil') !== false ? 'success' : '' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <p><strong>Order ID:</strong> <?= htmlspecialchars($order_id) ?></p>
        <p><strong>Name:</strong> <?= htmlspecialchars($nama) ?></p>
        <p><strong>Event Date:</strong> <?= htmlspecialchars($tanggal_event) ?></p>
        <p><strong>Total payment:</strong> Rp<?= number_format($total_harga, 0, ',', '.') ?></p>

        <form action="" method="post" enctype="multipart/form-data">
            <div class="section-title">Select Payment Type</div>
            <div class="radio-group">
                <label class="radio-option">
                    <input type="radio" name="payment_type" value="dp" checked onclick="updateAmount()">
                    <span>Pay 50% DP (Rp<?= number_format($dp_amount, 0, ',', '.') ?>)</span>
                </label>
                <label class="radio-option">
                    <input type="radio" name="payment_type" value="full" onclick="updateAmount()">
                    <span>Pay in Full (Rp<?= number_format($total_harga, 0, ',', '.') ?>)</span>
                </label>
            </div>

            <div class="section-title">Choose Payment Method</div>
            <div class="radio-group">
                <label class="radio-option">
                    <input type="radio" name="bank" value="bri" onclick="showInstruction('bri')">
                    <img src="img/bri.jpg" alt="BRI">
                    <span>Transfer to BRI (557601021383534 a.n Ristian)</span>
                </label>
                <label class="radio-option">
                    <input type="radio" name="bank" value="seabank" onclick="showInstruction('seabank')">
                    <img src="img/seabank.jpg" alt="SeaBank">
                    <span>Transfer to SeaBank (901212014985 a.n Ristian)</span>
                </label>
                <label class="radio-option">
                    <input type="radio" name="bank" value="dana" onclick="showInstruction('dana')">
                    <img src="img/dana.jpg" alt="Dana">
                    <span>Transfer to Dana (082293842967 a.n Ristian)</span>
                </label>
            </div>

            <div id="instruction" class="instruction"></div>

            <div class="section-title">Upload Proof of Payment</div>
            <input type="file" name="bukti" accept="image/*,.pdf" required>

            <button type="submit" class="btn">Upload Proof of Payment</button>
        </form>
    </div>

    <script>
        function updateAmount() {
            document.getElementById("instruction").style.display = "none";
        }

        function showInstruction(method) {
            const type = document.querySelector('input[name="payment_type"]:checked').value;
            const total = <?= $total_harga ?>;
            const dp = total * 0.5;
            let amount = type === "dp" ? dp : total;
            let rekening = "";
            let langkah = "";

            if (method === "bri") {
                rekening = "557601021383534 a.n Ristian (Bank BRI)";
                langkah = `
                <ol>
                    <li>Open your mobile banking app or visit an ATM.</li>
                    <li>Select the Transfer menu > To BRI Bank.</li>
                    <li>Enter the account number: <strong>557601021383534</strong></li>
                    <li>Enter the amount: <strong>Rp${amount.toLocaleString('id-ID')}</strong></li>
                    <li>Ensure the recipient's name is: <strong>Ristian</strong></li>
                    <li>Confirm and complete the transfer.</li>
                    <li>Save the payment receipt and upload it on this page.</li>
                </ol>

            `;
            } else if (method === "seabank") {
                rekening = "901212014985 a.n Ristian (SeaBank)";
                langkah = `
                <ol>
                    <li>Open the SeaBank app or a connected e-wallet.</li>
                    <li>Select the Transfer menu.</li>
                    <li>Enter the account number: <strong>901212014985</strong></li>
                    <li>Enter the amount: <strong>Rp${amount.toLocaleString('id-ID')}</strong></li>
                    <li>Ensure the recipient's name is: <strong>Ristian</strong></li>
                    <li>Confirm and complete the transfer.</li>
                    <li>Save the payment receipt and upload it on this page.</li>
                </ol>

            `;
            } else if (method === "dana") {
                rekening = "082293842967 a.n Ristian (Dana/E-Wallet)";
                langkah = `
                <ol>
                    <li>Open the Dana app.</li>
                    <li>Select the Send Money menu > Send to Phone Number.</li>
                    <li>Enter the number: <strong>082293842967</strong></li>
                    <li>Enter the amount: <strong>Rp${amount.toLocaleString('id-ID')}</strong></li>
                    <li>Ensure the recipient's name is: <strong>Ristian</strong></li>
                    <li>Confirm and complete the payment.</li>
                    <li>Save the payment receipt and upload it on this page.</li>
                </ol>

            `;
            }

            const box = document.getElementById("instruction");
            box.innerHTML = `
            <h3 class="highlight">Instruksi Pembayaran ke ${rekening}</h3>
            ${langkah}
        `;
            box.style.display = "block";
        }
    </script>
</body>

</html>