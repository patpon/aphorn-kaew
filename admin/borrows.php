<?php session_start();
require_once '../config.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการยืม-คืน —
        <?= SITE_NAME ?> Admin
    </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body class="admin-body">
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar" id="admin-sidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <span class="brand-icon">👗</span>
                <div>
                    <h2>
                        <?= SITE_NAME ?>
                    </h2>
                    <span class="brand-sub">Admin Panel</span>
                </div>
            </div>
            <button class="sidebar-close" id="sidebar-close" onclick="toggleSidebar()">✕</button>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item" id="nav-dashboard">
                <span class="nav-icon">📊</span>
                <span>Dashboard</span>
            </a>
            <a href="suits.php" class="nav-item" id="nav-suits">
                <span class="nav-icon">👔</span>
                <span>จัดการชุด</span>
            </a>
            <a href="borrows.php" class="nav-item active" id="nav-borrows">
                <span class="nav-icon">📋</span>
                <span>รายการยืม-คืน</span>
            </a>
        </nav>
        <div class="sidebar-footer">
            <div class="admin-info">
                <span class="admin-avatar">👤</span>
                <span class="admin-name" id="admin-name">Admin</span>
            </div>
            <button class="btn btn-sm btn-outline" onclick="adminLogout()">ออกจากระบบ</button>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="admin-main">
        <header class="admin-header">
            <button class="hamburger" id="hamburger" onclick="toggleSidebar()">☰</button>
            <h1>📋 รายการยืม-คืน</h1>
            <div class="header-right">
                <button class="btn btn-outline" onclick="exportCSV()">📥 Export CSV</button>
            </div>
        </header>

        <main class="admin-content">
            <!-- Filters -->
            <div class="filters-bar glass-card">
                <div class="filter-group">
                    <input type="text" id="borrows-search" placeholder="🔍 ค้นหาชื่อ, เบอร์, รหัส..."
                        onkeyup="debounceSearch()">
                </div>
                <div class="filter-group">
                    <select id="borrows-status-filter" onchange="loadBorrowsAdmin()">
                        <option value="">-- ทุกสถานะ --</option>
                        <option value="borrowed">ยืมอยู่</option>
                        <option value="returned">คืนแล้ว</option>
                        <option value="overdue">เกินกำหนด</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="table-wrapper">
                <table class="data-table admin-table" id="borrows-table">
                    <thead>
                        <tr>
                            <th>รหัส</th>
                            <th>ชื่อ</th>
                            <th>เบอร์โทร</th>
                            <th>ชุด</th>
                            <th>ไซส์</th>
                            <th>มัดจำ</th>
                            <th>วันที่ยืม</th>
                            <th>กำหนดคืน</th>
                            <th>สถานะ</th>
                        </tr>
                    </thead>
                    <tbody id="borrows-tbody">
                        <tr>
                            <td colspan="9" class="loading-cell">
                                <div class="spinner"></div>
                                <p>กำลังโหลด...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div class="toast-container" id="toast-container"></div>
    <script src="../assets/js/admin.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            loadBorrowsAdmin();
        });
    </script>
</body>

</html>