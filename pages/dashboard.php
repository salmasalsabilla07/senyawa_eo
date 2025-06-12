<?php
session_start();
include('config.php');

// Enable error reporting for debugging (DEVELOPMENT ONLY)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check admin authentication
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Function to handle PHP query errors
function executeQuery($conn, $query, $defaultValue = 0) {
    $result = mysqli_query($conn, $query);
    if (!$result) {
        error_log("MySQL Error: " . mysqli_error($conn) . " - Query: " . $query);
        return $defaultValue;
    }
    return $result;
}


$dateToday = date("D, d M Y"); 
$today = date("Y-m-d");
$tomorrow = date("Y-m-d", strtotime("+1 day"));

$totalOrders = 0;
$totalEventsToday = 0;
$totalPending = 0; 
$totalEventsTomorrow = 0;
$qRecent = [];


// 1. Query Total Orders 
$resTotal = executeQuery($conn, "SELECT COUNT(*) as total FROM pemesanan");
if (is_object($resTotal)) {
    $totalOrders = mysqli_fetch_assoc($resTotal)['total'];
}

// Query Today's Events 
$resTodaySchedule = executeQuery($conn, "SELECT COUNT(*) as jumlah FROM jadwal WHERE tanggal_event = '$today'");
if (is_object($resTodaySchedule)) {
    $totalEventsToday = mysqli_fetch_assoc($resTodaySchedule)['jumlah'];
}

// Query Pending Payments 
$resUnverified = executeQuery($conn, "SELECT COUNT(*) as pending FROM pembayaran WHERE status = 'pending'");
if (is_object($resUnverified)) {
    $totalPending = mysqli_fetch_assoc($resUnverified)['pending'];
}


$dateOneWeekAgo = date("Y-m-d"); 
$resRecent = executeQuery($conn, "SELECT nama, tanggal_event, nama_paket, status FROM pemesanan WHERE created_at >= '$dateOneWeekAgo' ORDER BY created_at DESC LIMIT 4");
if (is_object($resRecent)) {
    while ($row = mysqli_fetch_assoc($resRecent)) {
        $qRecent[] = $row;
    }
}

// Query for tomorrow's events 
$resTomorrowEvents = executeQuery($conn, "SELECT COUNT(*) as jumlah FROM jadwal WHERE tanggal_event = '$tomorrow'");
if (is_object($resTomorrowEvents)) {
    $totalEventsTomorrow = mysqli_fetch_assoc($resTomorrowEvents)['jumlah'];
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Event Organizer</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Global Styles */
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
            overflow: hidden; /* Prevent body scroll if content fits */
        }

        /* Sidebar Styles (assuming sidebar_dashboard.php has its own styles or you include them here) */
        /* If sidebar_dashboard.php directly contains HTML for the sidebar, its styles should be managed there,
           or pasted here if you want a single stylesheet. For this example, I'll assume basic sidebar styles
           are either included or managed externally, focusing on main content. */
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #1e1e1e 0%, #2a2a2a 100%);
            color: #ffffff;
            padding: 25px 20px;
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.3);
            display: flex;
            flex-direction: column;
            overflow-y: auto; /* Allow sidebar to scroll if content overflows */
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

        /* Main Content Styles */
        .main-content {
            flex: 1;
            padding: 32px;
            overflow-y: auto;
            background: linear-gradient(135deg, #2d2d2d 0%, #1a1a1a 100%);
        }

        .welcome-section {
            margin-bottom: 32px;
        }

        .welcome-title {
            font-size: 28px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 8px;
        }

        .welcome-subtitle {
            font-size: 16px;
            color: #999999;
        }

        /* STATS GRID */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); /* Adjusted minmax for better flexibility */
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: linear-gradient(135deg, #333333 0%, #2a2a2a 100%);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #404040;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #FFD700, #FFA500);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 32px rgba(255, 215, 0, 0.2);
        }

        /* Removed .stat-card.warning::before as the card is removed */

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .stat-title {
            font-size: 14px;
            font-weight: 500;
            color: #cccccc;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 215, 0, 0.1);
            color: #FFD700;
            font-size: 18px;
        }

        /* Specific icon colors if needed */
        .stat-icon.warning {
            background: rgba(255, 71, 87, 0.1);
            color: #ff4757;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 8px;
        }

        .stat-description {
            font-size: 12px;
            color: #999999;
        }

        /* Removed .warning-content and .warning-text, .verify-btn styles as they are no longer used */

        /* TABLE SECTION */
        .table-section {
            background: linear-gradient(135deg, #333333 0%, #2a2a2a 100%);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #404040;
            margin-bottom: 24px;
        }

        .table-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .table-title {
            font-size: 20px;
            font-weight: 600;
            color: #ffffff;
        }

        .table-container {
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid #404040;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            background: #2a2a2a;
        }

        .data-table th {
            background: linear-gradient(135deg, #404040, #333333);
            color: #FFD700;
            font-weight: 600;
            font-size: 14px;
            padding: 16px;
            text-align: left;
            border-bottom: 2px solid #555555;
        }

        .data-table td {
            padding: 16px;
            border-bottom: 1px solid #404040;
            color: #cccccc;
            font-size: 14px;
        }

        .data-table tr:hover {
            background: rgba(255, 215, 0, 0.05);
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .status-success {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
            border: 1px solid #22c55e;
        }

        .status-pending {
            background: rgba(251, 191, 36, 0.2);
            color: #fbbf24;
            border: 1px solid #fbbf24;
        }

        .status-gagal-transaksi {
            background: rgba(255, 71, 87, 0.2);
            color: #ff4757;
            border: 1px solid #ff4757;
        }


        /* EVENT REMINDER */
        .event-reminder {
            display: flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(135deg, #FFD700, #FFA500);
            color: #1a1a1a;
            padding: 16px 20px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 16px rgba(255, 215, 0, 0.3);
            margin-top: 24px;
        }

        .event-reminder i {
            font-size: 18px;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
                height: auto; /* Allow body to grow */
            }
            
            .main-content {
                padding: 20px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .welcome-title {
                font-size: 24px;
            }

            /* For table responsiveness if needed on small screens (already covered in previous CSS) */
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
            background: #FFD700;
        }
    </style>
</head>
<body>

<?php include('sidebar_dashboard.php'); ?>

<main class="main-content">
    <div class="welcome-section">
        <h1 class="welcome-title">Welcome, <?= htmlspecialchars($_SESSION['user'] ?? 'Admin') ?>!</h1>
        <p class="welcome-subtitle">Manage your event organizer system easily and efficiently</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title">Total Orders</div> <div class="stat-icon">
                    <i class="bi bi-receipt"></i>
                </div>
            </div>
            <div class="stat-value" id="totalOrdersValue"><?= $totalOrders ?></div>
            <div class="stat-description">All recorded orders</div> </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title">Today's Date</div>
                <div class="stat-icon">
                    <i class="bi bi-calendar-check"></i>
                </div>
            </div>
            <div class="stat-value" id="dateTodayValue" style="font-size: 20px;"><?= $dateToday ?></div>
            <div class="stat-description">System running normally</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title">Today's Events Schedule</div>
                <div class="stat-icon">
                    <i class="bi bi-calendar-event"></i>
                </div>
            </div>
            <div class="stat-value" id="totalEventsTodayValue"><?= $totalEventsToday ?></div>
            <div class="stat-description">Events scheduled for today</div>
        </div>

        </div>

<div class="table-section">
        <div class="table-header">
            <h3 class="table-title">Today's Orders</h3>
        </div>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Package</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="recentOrdersTableBody">
                    <tr><td colspan="4" style="text-align: center; color: #999;">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($totalEventsTomorrow > 0): ?>
    <div class="event-reminder">
        <i class="bi bi-megaphone-fill"></i>
        <span id="eventsTomorrowReminder"><?= $totalEventsTomorrow ?> Events starting tomorrow</span>
    </div>
    <?php endif; ?>
</main>

<script>
    function fetchData() {
        fetch('get_dashboard_data.php') 
            .then(response => {
                if (!response.ok) {
                    console.error('Network response was not ok:', response.status, response.statusText);
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received data:', data); 
                
                // Update Total Orders
                if (data.totalOrders !== undefined) {
                    document.getElementById('totalOrdersValue').textContent = data.totalOrders;
                }

                // Update Today's Date
                if (data.dateToday) {
                    document.getElementById('dateTodayValue').textContent = data.dateToday;
                }

                // Update Today's Events Schedule
                if (data.totalEventsToday !== undefined) {
                    document.getElementById('totalEventsTodayValue').textContent = data.totalEventsToday;
                }

                // Update Recent Orders Table
                const tableBody = document.getElementById('recentOrdersTableBody');
                if (tableBody) {
                    tableBody.innerHTML = ''; // Clear existing content

                    if (data.recentOrders && data.recentOrders.length > 0) {
                        data.recentOrders.forEach(order => {
                            const row = document.createElement('tr');
                            
                            // Handle status dengan lebih robust
                            const status = order.status_pembayaran ? order.status_pembayaran.toLowerCase() : '';
                            let statusClass = 'status-pending';
                            let displayStatus = order.status_pembayaran || 'Unknown';

                            if (status.includes('berhasil') || status.includes('sukses') || status.includes('success')) {
                                statusClass = 'status-success';
                                displayStatus = 'Payment Successful';
                            } else if (status.includes('menunggu') || status.includes('pending')) {
                                statusClass = 'status-pending';
                                displayStatus = 'Awaiting Payment';
                            } else if (status.includes('gagal') || status.includes('failed')) {
                                statusClass = 'status-gagal-transaksi';
                                displayStatus = 'Failed Transaction';
                            }

                            // Format tanggal
                            let eventDate = 'N/A';
                            if (order.tanggal_event) {
                                try {
                                    const date = new Date(order.tanggal_event);
                                    eventDate = date.toLocaleDateString('en-GB', { 
                                        day: '2-digit', 
                                        month: 'short' 
                                    });
                                } catch (e) {
                                    eventDate = order.tanggal_event;
                                }
                            }
                            
                            row.innerHTML = `
                                <td>${order.nama_lengkap || 'N/A'}</td>
                                <td>${eventDate}</td>
                                <td>${order.nama_paket || 'N/A'}</td>
                                <td>
                                    <span class="status-badge ${statusClass}">
                                        ${displayStatus}
                                    </span>
                                </td>
                            `;
                            tableBody.appendChild(row);
                        });
                    } else {
                        tableBody.innerHTML = '<tr><td colspan="4" style="text-align: center; color: #999;">No order data in the last 7 days</td></tr>';
                    }
                }

                // Update event reminder
                if (data.totalEventsTomorrow !== undefined) {
                    const reminderElement = document.getElementById('eventsTomorrowReminder');
                    const eventReminderDiv = document.querySelector('.event-reminder');
                    if (data.totalEventsTomorrow > 0) {
                        if (reminderElement) {
                             reminderElement.textContent = `${data.totalEventsTomorrow} Events starting tomorrow`;
                        }
                        if (eventReminderDiv) {
                            eventReminderDiv.style.display = 'flex';
                        }
                    } else {
                        if (eventReminderDiv) {
                            eventReminderDiv.style.display = 'none';
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching dashboard data:', error);
                const tableBody = document.getElementById('recentOrdersTableBody');
                if (tableBody) {
                    tableBody.innerHTML = '<tr><td colspan="4" style="text-align: center; color: red;">Failed to load data</td></tr>';
                }
            });
    }

    // Call fetchData when the page first loads
    document.addEventListener('DOMContentLoaded', fetchData);
    
    // Call fetchData every 30 seconds (optional - untuk auto refresh)
    setInterval(fetchData, 30000);
</script>

</body>
</html>