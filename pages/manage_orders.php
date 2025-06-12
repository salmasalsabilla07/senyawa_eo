<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['action'])) {
    $order_id = intval($_POST['id']);
    $action = $_POST['action'];

    $new_status = '';
    if ($action === 'terima') {
        $new_status = 'Berhasil';
    } elseif ($action === 'tolak') {
        $new_status = 'Gagal';
    }

    if (!empty($new_status)) {
        $stmt = $conn->prepare("UPDATE bookings SET status_pembayaran = ? WHERE id = ? AND status_pembayaran = 'pending'");
        $stmt->bind_param('si', $new_status, $order_id);
        $result = $stmt->execute();

        if ($result && $stmt->affected_rows > 0) {
            $_SESSION['success'] = "Status pembayaran untuk Order #$order_id berhasil diubah menjadi $new_status.";
        } else {
            $_SESSION['error'] = "Tidak dapat memperbarui status pembayaran untuk Order #$order_id.";
        }

        $stmt->close();
    }

    header("Location: manage_orders.php");
    exit;


    if ($update) {
        $_SESSION['success'] = "Payment status updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update status: " . mysqli_error($conn);
    }

    header("Location: manage_orders.php");
    exit;
}

$query = mysqli_query($conn, "
SELECT 
    b.id, b.nama_lengkap, b.email, b.nomor_telepon, b.jumlah, b.tanggal_event, b.created_at, b.status_pembayaran,
    p.name AS nama_paket, p.price, b.payment_type, b.bukti_pembayaran
FROM bookings b
JOIN packages p ON b.package_id = p.id
ORDER BY b.created_at DESC;
");


if (!$query) {
    die("Query gagal: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Manage Orders - Senyawa WO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        /* --- CSS tetap sama seperti yang kamu kirim, tidak diubah --- */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }

        body {
            display: flex;
            height: 100vh;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: #ffffff;
            font-size: 14px;
        }

        .wrapper {
            display: flex;
            width: 100%;
        }

        .main-content {
            flex: 1;
            background-color: #2c2c2c;
            padding: 20px;
            overflow-y: auto;
        }

        .card {
            background: linear-gradient(135deg, #404040 0%, #333333 100%);
            padding: 25px;
            border-radius: 12px;
            color: #ffffff;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            border: 1px solid #505050;
            margin-bottom: 25px;
        }

        .card h3 {
            margin-top: 0;
            font-size: 22px;
            margin-bottom: 20px;
            color: #ffd700;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
        }

        .card h3 i {
            font-size: 20px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background-color: #1e1e1e;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            font-size: 0.85em;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #3a3a3a;
            padding: 10px 15px;
            text-align: left;
            vertical-align: middle;
            max-width: 200px;
            word-wrap: break-word;
        }

        .data-table th {
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            color: #1e1e1e;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 11px;
        }

        .data-table td {
            background-color: #2c2c2c;
            color: #e0e0e0;
            transition: background-color 0.3s ease;
        }

        .data-table tbody tr:hover td {
            background-color: #333333;
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 8px;
            border-radius: 5px;
            font-size: 0.75em;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1e1e1e;
            white-space: nowrap;
        }

        .status-badge.pending {
            background-color: #ffc107;
            color: #494949;
        }

        .status-badge.berhasil {
            background-color: #28a745;
            color: #ffffff;
        }

        .status-badge.gagal {
            background-color: #dc3545;
            color: #ffffff;
        }

        .bukti-img {
            max-width: 100px;
            max-height: 80px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            object-fit: cover;
        }

        .btn-verify,
        .btn-tolak {
            border: none;
            color: white;
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
            white-space: nowrap;
            transition: background-color 0.3s ease;
        }

        .btn-verify {
            background-color: #28a745;
        }

        .btn-verify:hover {
            background-color: #218838;
        }

        .btn-tolak {
            background-color: #dc3545;
        }

        .btn-tolak:hover {
            background-color: #b52b2b;
        }

        .alert {
            padding: 12px 20px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .no-data {
            text-align: center;
            padding: 25px;
            color: #999;
            font-style: italic;
            background-color: #2c2c2c;
            border-radius: 8px;
            margin-top: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar_dashboard.php'; ?>
        <div class="main-content">
            <div class="card">
                <h3><i class="bi bi-receipt"></i> Manage Orders</h3>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($_SESSION['error']) ?></div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <?php if (mysqli_num_rows($query) > 0): ?>
                    <table class="data-table" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Order</th>
                                <th>Package</th>
                                <th>Price</th>
                                <th>Total</th> 
                                <th>Event Date</th>
                                <th>Order Date</th>
                                <th>Payment Method</th> 
                                <th>Payment Proof</th> 
                                <th>Status</th>
                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($query)): ?>
                                <?php
                                // Normalize status to lowercase for logic
                                $status = strtolower(trim($row['status_pembayaran']));
                                // Mapping status ke badge class & tampilkan teks yang user friendly
                                switch ($status) {
                                    case 'berhasil':
                                        $badgeClass = 'berhasil';
                                        $statusText = 'Succeed';
                                        break;
                                    case 'gagal':
                                        $badgeClass = 'gagal';
                                        $statusText = 'Fail';
                                        break;
                                    default:
                                        $badgeClass = 'pending';
                                        $statusText = 'pending';
                                }
                                ?>
                                <tr>
                                    <td>#<?= htmlspecialchars($row['id']) ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($row['nama_lengkap']) ?></strong><br>
                                        <small><?= htmlspecialchars($row['email']) ?></small><br>
                                        <small><?= htmlspecialchars($row['nomor_telepon']) ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($row['nama_paket']) ?></td>
                                    <td>Rp <?= number_format($row['price'], 0, ',', '.') ?></td>
                                    <td><?= intval($row['jumlah']) ?></td>
                                    <td><?= date('d M Y', strtotime($row['tanggal_event'])) ?></td>
                                    <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                                    <td><?= htmlspecialchars($row['payment_type'] ?? '-') ?></td>
                                    <td>
                                        <?php
                                        $buktiPath = 'uploads/' . $row['bukti_pembayaran'];
                                        if (!empty($row['bukti_pembayaran']) && file_exists($buktiPath)):
                                        ?>
                                            <a href="<?= htmlspecialchars($buktiPath) ?>" target="_blank" rel="noopener noreferrer">
                                                <img src="<?= htmlspecialchars($buktiPath) ?>" alt="Bukti Pembayaran" class="bukti-img" />
                                            </a>
                                        <?php else: ?>
                                            <small>There isn't any</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge <?= $badgeClass ?>">
                                            <?= htmlspecialchars($statusText) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
    if ($status === 'pending' && !empty($row['bukti_pembayaran'])): ?>
<form method="post" style="display:inline-block;">
    <input type="hidden" name="id" value="<?= $row['id'] ?>" />
    <input type="hidden" name="action" value="terima" />
    <button type="submit" class="btn-verify" onclick="return confirm('Verifikasi pembayaran?')">Accept</button>
</form>
<form method="post" style="display:inline-block;">
    <input type="hidden" name="id" value="<?= $row['id'] ?>" />
    <input type="hidden" name="action" value="tolak" />
    <button type="submit" class="btn-tolak" onclick="return confirm('Tolak pembayaran?')">Reject</button>
</form>
    <?php else: ?>
        <small>-</small>
    <?php endif; ?>
</td>


                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-data">No Booking yet</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>
