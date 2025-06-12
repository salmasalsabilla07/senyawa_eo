<?php

function is_active_page($page_name) {
    $current_page = basename($_SERVER['PHP_SELF']);
    return ($current_page == $page_name) ? 'active' : '';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* SIDEBAR */
        .sidebar {
            background: linear-gradient(180deg, #2a2a2a 0%, #1a1a1a 100%);
            width: 280px;
            padding: 24px 0;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #404040;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.3);
            min-height: 100vh; /* Ensure sidebar takes full height */
        }

        .sidebar-header {
            padding: 0 24px 24px;
            border-bottom: 1px solid #404040;
            margin-bottom: 16px;
        }

        .sidebar-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 18px;
            font-weight: 600;
            color: #FFD700;
        }

        .sidebar-title i {
            font-size: 20px;
        }

        .nav-menu {
            flex: 1;
            padding: 0 16px;
        }

        .nav-item {
            margin-bottom: 8px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: #cccccc;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .nav-link:hover {
            background: rgba(255, 215, 0, 0.1);
            color: #FFD700;
        }

        .nav-link.active {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: #1a1a1a;
        }

        .nav-link i {
            font-size: 16px;
            width: 20px;
        }

        .logout-section {
            padding: 16px;
            border-top: 1px solid #404040;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .logout-btn:hover {
            background: #c82333;
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
            }
        }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-title">
            <i class="bi bi-grid-3x3-gap-fill"></i>
            Dashboard Admin
        </div>
    </div>
    
    <nav class="nav-menu">
        <div class="nav-item">
            <a href="dashboard.php" class="nav-link <?php echo is_active_page('dashboard.php'); ?>">
                <i class="bi bi-house-fill"></i>
                Home
            </a>
        </div>
        <div class="nav-item">
            <a href="manage_orders.php" class="nav-link <?php echo is_active_page('manage_orders.php'); ?>">
                <i class="bi bi-receipt"></i>
                Manage Orders
            </a>
        </div>
        <div class="nav-item">
            <a href="manage_packages.php" class="nav-link <?php echo is_active_page('manage_packages.php'); ?>">
                <i class="bi bi-box-seam"></i>
                Manage Service Packages
            </a>
        </div>
        <div class="nav-item">
            <a href="manage_schedule.php" class="nav-link <?php echo is_active_page('manage_schedule.php'); ?>">
                <i class="bi bi-calendar-event"></i>
                Manage Schedule
            </a>
        </div>
        <div class="nav-item">
            <a href="reports.php" class="nav-link <?php echo is_active_page('reports.php'); ?>">
                <i class="bi bi-bar-chart-line"></i>
                Order report
            </a>
        </div>
        <div class="nav-item">
            <a href="user_list.php" class="nav-link <?php echo is_active_page('user_list.php'); ?>">
                <i class="bi bi-people-fill"></i>
                User Management
            </a>
        </div>
    </nav>
    
    <div class="logout-section">
        <a href="logout.php" class="logout-btn">
            <i class="bi bi-box-arrow-right"></i>
            Logout
        </a>
    </div>
</aside>

</body>
</html>