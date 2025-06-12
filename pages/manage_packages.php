<?php
session_start(); 
include 'config.php'; 

if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$editMode = false;
$editData = null;

// Simpan paket
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = intval($_POST['price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    if ($id) {
        $sql = "UPDATE packages SET name='$name', category='$category', price='$price', description='$description' WHERE id=$id";
    } else {
        $sql = "INSERT INTO packages (name, category, price, description) VALUES ('$name', '$category', '$price', '$description')";
    }

    if (mysqli_query($conn, $sql)) {
        header('Location: manage_packages.php');
        exit;
    } else {
        echo "Gagal menyimpan data: " . mysqli_error($conn);
    }
}

// Edit paket
if (isset($_GET['edit'])) {
    $editMode = true;
    $id = intval($_GET['edit']);
    $result = mysqli_query($conn, "SELECT * FROM packages WHERE id=$id");
    $editData = mysqli_fetch_assoc($result);
}

// Hapus paket
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM packages WHERE id=$id");
    header('Location: manage_packages.php');
    exit;
}

// Ambil semua paket
$packages = mysqli_query($conn, "SELECT * FROM packages ORDER BY id DESC");

// Daftar kategori
$packageCategories = [
    'engagement' => 'Engagement',
    'akad' => 'Akad Nikah',
    'reception' => 'Resepsi',
    'intimate' => 'Intimate Wedding'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manage Packages - Senyawa WO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* General styles, similar to your dashboard.php */
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
        }

        .wrapper {
            display: flex;
            width: 100%; /* Ensure wrapper takes full width */
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            background-color: #2c2c2c;
            padding: 30px;
            overflow-y: auto;
        }

        .card {
            background: linear-gradient(135deg, #404040 0%, #333333 100%);
            padding: 30px;
            border-radius: 12px;
            color: #ffffff;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            border: 1px solid #505050;
            margin-bottom: 30px;
        }

        .card h3 {
            margin-top: 0;
            font-size: 24px;
            margin-bottom: 25px;
            color: #ffd700;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
        }

        .card h3 i {
            font-size: 22px;
        }

        /* Form Styles */
        .form-group,
        .form-group.full-width {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #ffd700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-input,
        .form-textarea,
        .form-select {
            width: 100%;
            padding: 12px 16px;
            border-radius: 8px;
            border: 2px solid #505050;
            font-size: 14px;
            background-color: #1e1e1e;
            color: #ffffff;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .form-input:focus,
        .form-textarea:focus,
        .form-select:focus {
            outline: none;
            border-color: #ffd700;
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
            font-family: inherit;
        }

        .form-select {
            cursor: pointer;
        }

        .form-select option {
            background-color: #1e1e1e;
            color: #ffffff;
        }

        .form-buttons {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            color: #1e1e1e;
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #ffed4e 0%, #ffd700 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3);
        }

        .btn-secondary {
            background-color: #6c757d;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
            color: #ffffff;
            text-decoration: none;
        }

        /* Table Styles */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #1e1e1e;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        .data-table th,
        .data-table td {
            padding: 16px 20px;
            text-align: left;
            border-bottom: 1px solid #404040;
        }

        .data-table th {
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            color: #1e1e1e;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 13px;
        }

        .data-table td {
            background-color: #2c2c2c;
            color: #ffffff;
            transition: background-color 0.3s ease;
        }

        .data-table tbody tr:hover td {
            background-color: #333333;
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        .data-table td strong {
            color: #ffd700; /* Use a specific color for consistency */
            font-weight: 600;
        }

        .data-table td small {
            color: #b3b3b3;
            font-size: 12px;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
        }

        .action-buttons a {
            color: #ffd700;
            text-decoration: none;
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 6px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
        }

        .action-buttons a:hover {
            background-color: rgba(255, 215, 0, 0.2); /* A subtle background on hover */
            color: #ffd700; /* Keep the text color gold */
            text-decoration: none;
            transform: translateY(-1px);
        }

        .action-buttons a i {
            font-size: 14px;
        }

        /* Empty state */
        .data-table td[colspan="3"] {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 40px 20px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .wrapper {
                flex-direction: column;
                height: auto;
            }
            
            /* Hide the sidebar for smaller screens, or implement a mobile toggle */
            .sidebar {
                display: none; 
            }
            
            .main-content {
                padding: 20px;
            }
            
            .card {
                padding: 20px;
            }
            
            .form-buttons {
                flex-direction: column;
            }
            
            .data-table {
                font-size: 12px;
            }
            
            .data-table th,
            .data-table td {
                padding: 12px 8px;
            }
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #1e1e1e;
        }

        ::-webkit-scrollbar-thumb {
            background: #555555;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #FFD700; /* Change to a solid color or relevant gradient */
        }
    </style>
</head>
<body>
<div class="wrapper">
    <?php include 'sidebar_dashboard.php'; ?>

    <div class="main-content">
        <div class="card">
            <h3><i class="bi bi-<?= $editMode ? 'pencil-square' : 'plus-circle' ?>"></i> <?= $editMode ? 'Edit Package' : 'Add New Package' ?></h3>
            <form method="POST">
                <?php if ($editMode && $editData): ?>
                    <input type="hidden" name="id" value="<?= $editData['id'] ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label class="form-label">Package Name</label>
                    <input type="text" name="name" class="form-input" required value="<?= $editData['name'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select" required>
                        <option value="">Select Category</option>
                        <?php foreach ($packageCategories as $key => $label): ?>
                            <option value="<?= $key ?>" <?= (isset($editData['category']) && $editData['category'] == $key) ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Price (Rp)</label>
                    <input type="number" name="price" class="form-input" required value="<?= $editData['price'] ?? '' ?>">
                </div>

                <div class="form-group full-width">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-textarea" rows="4" required><?= $editData['description'] ?? '' ?></textarea>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn-primary"><i class="bi bi-<?= $editMode ? 'check-circle' : 'plus-circle' ?>"></i> <?= $editMode ? 'Update Package' : 'Add Package' ?></button>
                    <?php if ($editMode): ?>
                        <a href="manage_packages.php" class="btn-secondary"><i class="bi bi-x-circle"></i> Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="card" style="margin-top: 30px;">
            <h3><i class="bi bi-list-ul"></i> Package List</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Package Name</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($packages && mysqli_num_rows($packages) > 0): ?>
                    <?php while ($pkg = mysqli_fetch_assoc($packages)): ?>
                        <tr>
                            <td>
                                <strong><?= $pkg['name'] ?></strong> <small>(<?= $packageCategories[$pkg['category']] ?? $pkg['category'] ?>)</small><br>
                                <small><?= substr($pkg['description'], 0, 100) ?>...</small>
                            </td>
                            <td>Rp <?= number_format($pkg['price'], 0, ',', '.') ?></td>
                            <td class="action-buttons">
                                <a href="?edit=<?= $pkg['id'] ?>"><i class="bi bi-pencil"></i> Edit</a>
                                <a href="?delete=<?= $pkg['id'] ?>" onclick="return confirm('Are you sure you want to delete this package?')"><i class="bi bi-trash"></i> Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align: center; color: #999;">There are no packages yet.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>