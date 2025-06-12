<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();


// Koneksi database
$conn = new mysqli("localhost", "root", "", "senyawa_wo");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Cek login
if (!isset($_SESSION['user'])) {
    header("Location: ../pages/login.php");
    exit();
}
$username = $_SESSION['user'];

// Fungsi upload foto profil
function uploadFoto($file, $oldFile = null) {
    global $username;
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 2 * 1024 * 1024; // 2MB

    if ($file['error'] === UPLOAD_ERR_NO_FILE) return $oldFile;
    if ($file['error'] !== UPLOAD_ERR_OK || !in_array($file['type'], $allowed_types) || $file['size'] > $max_size) {
        return false;
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newName = $username . '_' . time() . '.' . $ext;
    $upload_dir = __DIR__ . '/uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    if (move_uploaded_file($file['tmp_name'], $upload_dir . $newName)) {
        if ($oldFile && file_exists($upload_dir . $oldFile) && $oldFile !== $newName) {
            unlink($upload_dir . $oldFile);
        }
        return $newName;
    }
    return false;
}

// Fungsi untuk menampilkan status dengan warna
function statusBadge($status) {
    $status_lower = strtolower($status);
    $color = 'gray';
    switch($status_lower) {
        case 'berhasil': $color = 'green'; break;
        case 'pending': $color = 'orange'; break;
        case 'gagal': $color = 'red'; break;
        case 'proses': $color = 'blue'; break;
        case 'selesai': $color = 'darkgreen'; break;
        // Tambah status lain jika ada
    }
    return "<span style='color:$color; font-weight:bold; text-transform: capitalize;'>".htmlspecialchars($status)."</span>";
}

// Ambil data profil user
$stmt = $conn->prepare("SELECT * FROM user_profile WHERE username = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $username);
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();

// Ambil password user
$stmt2 = $conn->prepare("SELECT password FROM users WHERE username = ?");
if (!$stmt2) {
    die("Prepare failed: " . $conn->error);
}
$stmt2->bind_param("s", $username);
$stmt2->execute();
$userPass = $stmt2->get_result()->fetch_assoc();

$mode = $_GET['mode'] ?? 'view';

// Proses update profil
if (isset($_POST['update_profile'])) {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    if ($nama === '' || $phone === '') {
        echo "<script>alert('Nama dan Nomor Telepon wajib diisi.');</script>";
    } else {
        $foto_baru = uploadFoto($_FILES['foto'], $userData['foto'] ?? null);
        if ($foto_baru === false) {
            echo "<script>alert('Upload foto gagal. Pastikan format jpg/png/gif dan ukuran < 2MB.');</script>";
        } else {
            // Cek apakah user sudah punya profile
            $check = $conn->prepare("SELECT username FROM user_profile WHERE username = ?");
            if (!$check) {
                die("Prepare failed: " . $conn->error);
            }
            $check->bind_param("s", $username);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                $update = $conn->prepare("UPDATE user_profile SET nama=?, email=?, phone=?, alamat=?, foto=? WHERE username=?");
                if (!$update) {
                    die("Prepare failed: " . $conn->error);
                }
                $update->bind_param("ssssss", $nama, $email, $phone, $alamat, $foto_baru, $username);
                $success = $update->execute();
            } else {
                $insert = $conn->prepare("INSERT INTO user_profile (username, nama, email, phone, alamat, foto) VALUES (?, ?, ?, ?, ?, ?)");
                if (!$insert) {
                    die("Prepare failed: " . $conn->error);
                }
                $insert->bind_param("ssssss", $username, $nama, $email, $phone, $alamat, $foto_baru);
                $success = $insert->execute();
            }
            if ($success) {
                header("Location: profile.php?mode=view");
                exit();
            } else {
                echo "<script>alert('Gagal menyimpan profil.');</script>";
            }
        }
    }
}

// Proses ganti password
if (isset($_POST['change_password_submit'])) {
    $password_lama = $_POST['password_lama'] ?? '';
    $password_baru = $_POST['password_baru'] ?? '';
    $password_baru_confirm = $_POST['password_baru_confirm'] ?? '';

    if ($password_baru !== $password_baru_confirm) {
        echo "<script>alert('Password baru dan konfirmasi tidak cocok.');</script>";
    } else if (!password_verify($password_lama, $userPass['password'])) {
        echo "<script>alert('Password lama salah.');</script>";
    } else {
        $new_password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
        $update_pass = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
        if (!$update_pass) {
            die("Prepare failed: " . $conn->error);
        }
        $update_pass->bind_param("ss", $new_password_hash, $username);
        if ($update_pass->execute()) {
            echo "<script>alert('Password berhasil diganti.'); window.location.href='profile.php?mode=view';</script>";
            exit();
        } else {
            echo "<script>alert('Gagal mengganti password.');</script>";
        }
    }
}

// Refresh data profil setelah update/ganti password
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();

// Ambil riwayat booking jika mode = history
if ($mode == 'history') {
    $query_history = "SELECT 
                        b.id, 
                        b.order_id, 
                        b.tanggal_event, 
                        p.name AS nama_paket, 
                        b.jumlah, 
                        b.bank, 
                        b.status_pembayaran, 
                        b.progress_status,
                        b.created_at 
                      FROM bookings b 
                      LEFT JOIN packages p ON b.package_id = p.id 
                      WHERE b.username = ? 
                      ORDER BY b.created_at DESC";
    $stmt_history = $conn->prepare($query_history);
    if (!$stmt_history) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
    $stmt_history->bind_param("s", $username);
    $stmt_history->execute();
    $result_history = $stmt_history->get_result();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Profil Saya - Senyawa WO</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #121212;
            color: #eee;
            margin: 0; padding: 20px;
        }
        .container {
            max-width: 720px;
            margin: auto;
            background: #222;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(243,156,18,0.4);
        }
        h2 {
            color: #f39c12;
            margin-bottom: 25px;
            text-align: center;
        }
        .profile-photo {
            display: block;
            margin: 15px auto 30px;
            border-radius: 50%;
            width: 130px; height: 130px;
            object-fit: cover;
            border: 4px solid #f39c12;
            box-shadow: 0 0 15px #f39c12;
        }
        p {
            font-size: 1.1rem;
            line-height: 1.6;
            margin: 8px 0;
            text-align: center;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: 600;
            color: #f39c12;
        }
        input[type=text],
        input[type=email],
        input[type=password],
        textarea {
            width: 100%;
            padding: 10px 14px;
            margin-top: 6px;
            border: none;
            border-radius: 6px;
            background: #333;
            color: #eee;
            font-size: 1rem;
            resize: vertical;
        }
        textarea {
            min-height: 70px;
        }
        button {
            margin-top: 25px;
            background: #f39c12;
            border: none;
            padding: 12px 28px;
            border-radius: 6px;
            color: #121212;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s ease;
        }
        button:hover {
            background: #d48806;
        }
        .nav {
            text-align: center;
            margin-bottom: 30px;
        }
        .nav a {
            color: #f39c12;
            margin: 0 12px;
            text-decoration: none;
            font-weight: 600;
        }
        .nav a.active {
            border-bottom: 2px solid #f39c12;
            padding-bottom: 2px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background: #1e1e1e;
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 12px 10px;
            border-bottom: 1px solid #333;
            text-align: center;
        }
        th {
            background: #f39c12;
            color: #121212;
            font-weight: 700;
        }
        tbody tr:hover {
            background: #333;
        }
    </style>
</head>
<body>
    
    <div class="container">
        <a href="../index.php" style="display:inline-block; margin-bottom:15px; color:#FFA500;">Back to Home</a>
        <h2>My Profile</h2>



        <div class="nav">
            <a href="profile.php?mode=view" class="<?= $mode == 'view' ? 'active' : '' ?>">View Profile</a> |
            <a href="profile.php?mode=edit" class="<?= $mode == 'edit' ? 'active' : '' ?>">Edit Profile</a> |
            <a href="profile.php?mode=changepass" class="<?= $mode == 'changepass' ? 'active' : '' ?>">Change Password</a> |
            <a href="profile.php?mode=history" class="<?= $mode == 'history' ? 'active' : '' ?>">Booking History</a>
        </div>
        

        <?php if ($mode == 'view'): ?>
            <img class="profile-photo" src="<?= isset($userData['foto']) && $userData['foto'] !== '' ? 'uploads/' . htmlspecialchars($userData['foto']) : 'https://www.w3schools.com/howto/img_avatar.png' ?>" alt="Foto Profil" />
            <p><strong>Username:</strong> <?= htmlspecialchars($username) ?></p>
            <p><strong>Full Name:</strong> <?= htmlspecialchars($userData['nama'] ?? '-') ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($userData['email'] ?? '-') ?></p>
            <p><strong>Phone Number:</strong> <?= htmlspecialchars($userData['phone'] ?? '-') ?></p>
            <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($userData['alamat'] ?? '-')) ?></p>

        <?php elseif ($mode == 'edit'): ?>
            <form method="POST" enctype="multipart/form-data">
                <label>Full name*</label>
                <input type="text" name="nama" required value="<?= htmlspecialchars($userData['nama'] ?? '') ?>" />

                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($userData['email'] ?? '') ?>" />

                <label>Phone number*</label>
                <input type="text" name="phone" required value="<?= htmlspecialchars($userData['phone'] ?? '') ?>" />

                <label>Address</label>
                <textarea name="alamat"><?= htmlspecialchars($userData['alamat'] ?? '') ?></textarea>

                <label>Profile picture</label>
                <input type="file" name="foto" accept="image/png, image/jpeg, image/gif" />
                <?php if (isset($userData['foto']) && $userData['foto'] !== ''): ?>
                    <p>Current Profile Picture<br><img src="uploads/<?= htmlspecialchars($userData['foto']) ?>" alt="Foto Profil" style="width:80px; border-radius:50%; margin-top:8px;"></p>
                <?php endif; ?>

                <button type="submit" name="update_profile">Save Changes</button>
            </form>

        <?php elseif ($mode == 'changepass'): ?>
            <form method="POST">
                <label>Old Password</label>
                <input type="password" name="password_lama" required />

                <label>New Password</label>
                <input type="password" name="password_baru" required />

                <label>Confirm New Password</label>
                <input type="password" name="password_baru_confirm" required />

                <button type="submit" name="change_password_submit">Change Password</button>
            </form>


        <?php elseif ($mode == 'history'): ?>
            <h3>Booking History</h3>
            <?php if ($result_history && $result_history->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Order ID</th>
                            <th>Event Date</th>
                            <th>Package Name</th>
                            <th>Amount</th>
                            <th>Bank</th>
                            <th>Payment Status</th>
                            <th>Progress Status</th>
                            <th>Booking Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; while($row = $result_history->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['order_id']) ?></td>
                            <td><?= htmlspecialchars($row['tanggal_event']) ?></td>
                            <td><?= htmlspecialchars($row['nama_paket'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['jumlah']) ?></td>
                            <td><?= htmlspecialchars($row['bank']) ?></td>
                            <td><?= statusBadge($row['status_pembayaran']) ?></td>
                            <td><?= statusBadge($row['progress_status']) ?></td>
                            <td><?= htmlspecialchars($row['created_at']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Tidak ada riwayat booking.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    
</body>
</html>
