<?php
session_start();
include('config.php');
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    $query = "UPDATE packages SET name=?, description=?, price=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ssdi', $name, $description, $price, $id);
    mysqli_stmt_execute($stmt);
    header("Location: manage_packages.php");
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM packages WHERE id = $id");
$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Paket</title>
</head>

<body>
    <h2>Edit Paket</h2>
    <form method="POST">
        <input type="text" name="name" value="<?= $data['name']; ?>" required><br>
        <textarea name="description" required><?= $data['description']; ?></textarea><br>
        <input type="number" name="price" value="<?= $data['price']; ?>" required><br>
        <button type="submit">Update</button>
    </form>
    <a href="manage_packages.php">← Kembali</a>
</body>

</html>