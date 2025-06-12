<?php
session_start();
include('config.php'); 

if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

function executeQuery($conn, $sql) {
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        error_log("SQL Error: " . mysqli_error($conn) . " Query: " . $sql);
        die("Query failed: " . mysqli_error($conn) . "<br>SQL: " . htmlspecialchars($sql));
    }
    return $result;
}

$result = executeQuery($conn, "SELECT * FROM users ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna - Senyawa EO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            overflow: hidden; 
        }

        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #1e1e1e 0%, #2a2a2a 100%);
            color: #ffffff;
            padding: 25px 20px;
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.3);
            display: flex;
            flex-direction: column;
            overflow-y: auto; 
            border-right: 1px solid #333333;
        }

        .sidebar-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid #444444;
        }

        .sidebar-header h2 {
            font-size: 28px;
            color: #ffd700;
            font-weight: 700;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .sidebar-header p {
            font-size: 14px;
            color: #b3b3b3;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            flex-grow: 1;
        }

        .sidebar-menu li {
            margin-bottom: 10px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #cccccc;
            text-decoration: none;
            font-size: 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
            gap: 12px;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: linear-gradient(90deg, rgba(255, 215, 0, 0.2) 0%, rgba(255, 215, 0, 0) 100%);
            color: #ffd700;
            transform: translateX(5px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar-menu a i {
            font-size: 18px;
        }

        .logout-link {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #444444;
        }

        .logout-link a {
            background-color: #dc3545;
            color: #ffffff;
            justify-content: center;
        }

        .logout-link a:hover {
            background-color: #c82333;
            color: #ffffff; 
            transform: none; 
        }


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
            text-decoration: none; 
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
            color: #ffd700;
            font-weight: 600;
        }

        .data-table td small {
            color: #b3b3b3;
            font-size: 12px;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap; 
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
            background-color: rgba(255, 215, 0, 0.2);
            color: #ffd700;
            text-decoration: none;
            transform: translateY(-1px);
        }

        .action-buttons a.delete-btn { 
            color: #ff4757;
        }

        .action-buttons a.delete-btn:hover {
            background-color: rgba(255, 71, 87, 0.2); 
            color: #ff4757;
        }

        .action-buttons a i {
            font-size: 14px;
        }

        .data-table td[colspan] {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 40px 20px;
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
                height: auto;
                padding: 0;
            }

            .sidebar {
                width: 100%;
                height: auto;
                padding: 20px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
                border-right: none;
                border-bottom: 1px solid #333333;
            }

            .sidebar-header {
                margin-bottom: 20px;
                padding-bottom: 15px;
            }

            .sidebar-menu {
                flex-direction: row;
                flex-wrap: wrap;
                justify-content: center;
                gap: 10px;
            }

            .sidebar-menu li {
                margin-bottom: 5px;
            }

            .sidebar-menu a {
                padding: 8px 12px;
                font-size: 14px;
                gap: 8px;
                text-align: center;
                flex-direction: column;
            }

            .sidebar-menu a i {
                font-size: 16px;
            }

            .logout-link {
                margin-top: 20px;
                padding-top: 15px;
            }

            .main-content {
                padding: 20px;
            }

            .card {
                padding: 20px;
            }

            .form-buttons {
                flex-direction: column;
                justify-content: center; 
            }

            .data-table {
                font-size: 12px;
                border-radius: 8px;
                margin-top: 15px;
            }

            .data-table th {
                padding: 12px 8px;
            }

            .data-table thead {
                display: none; 
            }
            .data-table tr {
                display: block; 
                margin-bottom: 15px;
                border: 1px solid #404040;
                border-radius: 8px;
                overflow: hidden;
            }
            .data-table td {
                display: block; 
                text-align: right;
                padding-left: 50%; 
                position: relative;
                border-bottom: 1px dashed #404040; 
            }
            .data-table td:last-child {
                border-bottom: none;
            }
            .data-table td::before {
                content: attr(data-label); 
                position: absolute;
                left: 10px;
                width: calc(50% - 20px);
                text-align: left;
                font-weight: 600;
                color: #ffd700;
                text-transform: uppercase;
                font-size: 10px;
            }
        }

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
            background: #FFD700;
        }
    </style>
</head>
<?php include 'sidebar_dashboard.php'; ?>

        <div class="main-content">
            <div class="card">
                <h3><i class="bi bi-people"></i> User Management</h3>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($user = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td data-label="ID"><?= htmlspecialchars($user['id']); ?></td>
                                    <td data-label="Username"><?= htmlspecialchars($user['username']); ?></td>
                                    <td data-label="Role"><?= htmlspecialchars($user['role']); ?></td>
                                    <td class="action-buttons" data-label="Action">
                                        <a href="delete_user.php?id=<?= htmlspecialchars($user['id']); ?>" onclick="return confirm('Delete this user?')" class="delete-btn"><i class="bi bi-trash"></i> Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">There are no registered users.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>