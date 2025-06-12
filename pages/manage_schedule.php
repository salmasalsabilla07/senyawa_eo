<?php
session_start();
include('config.php');

if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Proses update progress_status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'], $_POST['progress_status'])) {
    $booking_id = $_POST['booking_id'];
    $progress_status = $_POST['progress_status'];

    if (in_array($progress_status, ['on progress', 'done'])) {
        $stmt = $conn->prepare("UPDATE bookings SET progress_status = ? WHERE id = ?");
        $stmt->bind_param("si", $progress_status, $booking_id);
        $stmt->execute();
    }

    header("Location: manage_schedule.php");
    exit;
}

// Query data
$query = "
    SELECT 
        b.id,
        b.nama_lengkap,
        b.email,
        b.nomor_telepon,
        b.tanggal_event,
        b.status_pembayaran,
        b.jumlah,
        b.payment_type,
        b.progress_status,
        p.name AS package_name,
        br.nama_pria,
        br.nama_wanita,
        br.jam_event,
        br.lokasi_event
    FROM bookings b
    JOIN packages p ON b.package_id = p.id
    JOIN brides br ON b.order_id = br.order_id
    WHERE b.status_pembayaran = 'berhasil'
    ORDER BY b.tanggal_event ASC
";

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Schedule - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <style>
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

        .table-wrapper {
            overflow-x: auto;
            width: 100%;
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
            min-width: 1000px; /* supaya minimal lebar tabel */
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
            white-space: nowrap;
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

        select {
            background-color: #404040;
            color: #fff;
            border: none;
            padding: 6px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.85em;
        }

        select:focus {
            outline: none;
            box-shadow: 0 0 5px #ffd700;
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

        <main class="main-content">
            <div class="card">
                <h3><i class="fas fa-calendar-alt"></i> Manage Schedule</h3>

                <?php if (mysqli_num_rows($result) > 0) : ?>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Event Date</th>
                                    <th>Package</th>
                                    <th>Groom's Name</th>
                                    <th>Bride's Name</th>
                                    <th>Event Time</th>
                                    <th>Event Location</th>
                                    <th>Payment Status</th>
                                    <th>Total Amount</th>
                                    <th>Payment Method</th>
                                    <th>Progress Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                        <td><?= htmlspecialchars($row['email']) ?></td>
                                        <td><?= htmlspecialchars($row['nomor_telepon']) ?></td>
                                        <td><?= htmlspecialchars($row['tanggal_event']) ?></td>
                                        <td><?= htmlspecialchars($row['package_name']) ?></td>
                                        <td><?= htmlspecialchars($row['nama_pria']) ?></td>
                                        <td><?= htmlspecialchars($row['nama_wanita']) ?></td>
                                        <td><?= htmlspecialchars($row['jam_event']) ?></td>
                                        <td><?= htmlspecialchars($row['lokasi_event']) ?></td>
                                        <td><span class="status-badge berhasil"><?= htmlspecialchars($row['status_pembayaran']) ?></span></td>
                                        <td><?= htmlspecialchars($row['jumlah']) ?></td>
                                        <td><?= htmlspecialchars($row['payment_type']) ?></td>
                                        <td>
                                            <form method="POST" action="manage_schedule.php">
                                                <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">
                                                <select name="progress_status" onchange="this.form.submit()">
                                                    <option value="on progress" <?= $row['progress_status'] === 'on progress' ? 'selected' : '' ?>>On Progress</option>
                                                    <option value="done" <?= $row['progress_status'] === 'done' ? 'selected' : '' ?>>Done</option>
                                                </select>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <div class="no-data">No schedules found.</div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
