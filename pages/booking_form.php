<?php
session_start();

$conn = new mysqli("localhost", "root", "", "senyawa_wo");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil username dari sesi login
$username = $_SESSION['user'] ?? null;
if (!$username) {
    die("Anda harus login untuk mengakses halaman ini.");
}

$packages_result = $conn->query("SELECT * FROM packages");

$selected_package_id = $_GET['package_id'] ?? '';
$tanggal_event = $_GET['tanggal_event'] ?? '';

$packages_for_js = [];
$packages_for_select = [];

if ($packages_result->num_rows > 0) {
    while ($row = $packages_result->fetch_assoc()) {
        $packages_for_js[$row['id']] = (int)$row['price'];
        $packages_for_select[$row['id']] = $row['name'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nama_lengkap'])) {
    $order_id = $_POST['order_id'] ?? uniqid('order-');
    $package_id = $_POST['package_id'];
    $tanggal_event = $_POST['tanggal_event'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $nomor_telepon = $_POST['nomor_telepon'];
    $total_harga = $_POST['total_harga'];
    $status_pembayaran = 'pending';

    $stmt = $conn->prepare("INSERT INTO bookings (username, order_id, package_id, tanggal_event, nama_lengkap, email, nomor_telepon, jumlah, status_pembayaran) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissssis", $username, $order_id, $package_id, $tanggal_event, $nama_lengkap, $email, $nomor_telepon, $total_harga, $status_pembayaran);

    if ($stmt->execute()) {
        $_SESSION['order_id'] = $order_id;
        $_SESSION['total_harga'] = $total_harga;
        $_SESSION['package_id'] = $package_id;
        $_SESSION['tanggal_event'] = $tanggal_event;
        $_SESSION['nama_lengkap'] = $nama_lengkap;
        $_SESSION['email'] = $email;
        $_SESSION['nomor_telepon'] = $nomor_telepon;

        $stmt->close();
        $conn->close();

        header("Location: form_data_pengantin.php");
        exit;
    } else {
        echo "Gagal menyimpan ke database: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Order Form</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #111;
            color: #d6a84f;
            padding: 40px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #d6a84f;
        }

        form {
            background: #222;
            padding: 30px;
            max-width: 550px;
            margin: auto;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.4);
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #d6a84f;
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 10px;
            border: 1px solid #555;
            background-color: #222;
            color: #d6a84f;
            font-size: 16px;
        }

        input::placeholder {
            color: #bbb;
        }

        .row-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .row-2col>div {
            padding-right: 4px;
            padding-left: 4px;
        }

        @media (max-width: 600px) {
            .row-2col {
                grid-template-columns: 1fr;
            }
        }

        .btn {
            background-color: #d6a84f;
            color: #222;
            padding: 12px 20px;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background-color: #c1a040;
        }

        #price_display {
            font-size: 1.4rem;
            font-weight: bold;
            margin-bottom: 20px;
            color: #d6a84f;
            text-align: right;
        }
    </style>
</head>
<body>

    <h2>Package Order Form</h2>

    <form action="" method="POST" id="booking_form">
        <label for="package_id">Select Package:</label>
        <select name="package_id" id="package_id" required>
            <option value="">-- Select Package --</option>
            <?php foreach ($packages_for_select as $id => $name) : ?>
                <option value="<?= $id ?>" <?= ($id == $selected_package_id ? 'selected' : '') ?>>
                    <?= htmlspecialchars($name) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="tanggal_event">Event Date:</label>
        <input type="date" name="tanggal_event" id="tanggal_event" required value="<?= htmlspecialchars($tanggal_event) ?>">

        <label for="nama_lengkap">Full name:</label>
        <input type="text" name="nama_lengkap" id="nama_lengkap" required value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? '') ?>">

        <div class="row-2col">
            <div>
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div>
                <label for="nomor_telepon">Phone number:</label>
                <input type="text" name="nomor_telepon" id="nomor_telepon" required value="<?= htmlspecialchars($_POST['nomor_telepon'] ?? '') ?>">
            </div>
        </div>

        <div id="price_display">Total price: Rp0</div>

        <input type="hidden" name="order_id" value="<?= uniqid('order-') ?>">
        <input type="hidden" name="total_harga" id="total_harga" value="0">

        <button type="submit" class="btn">Next: Bride and Groom Details</button>
    </form>

    <script>
        const packagePrices = <?= json_encode($packages_for_js) ?>;
        const packageSelect = document.getElementById('package_id');
        const priceDisplay = document.getElementById('price_display');
        const totalHargaInput = document.getElementById('total_harga');

        function updatePrice() {
            const selectedId = packageSelect.value;
            if (selectedId && packagePrices[selectedId] !== undefined) {
                const price = packagePrices[selectedId];
                priceDisplay.textContent = 'Total Harga: Rp' + price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                totalHargaInput.value = price;
            } else {
                priceDisplay.textContent = 'Total Harga: Rp0';
                totalHargaInput.value = 0;
            }
        }

        updatePrice();
        packageSelect.addEventListener('change', updatePrice);
    </script>

</body>
</html>
