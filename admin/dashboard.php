<?php session_start();
require_once '../config.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard —
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
            <a href="dashboard.php" class="nav-item active" id="nav-dashboard">
                <span class="nav-icon">📊</span>
                <span>Dashboard</span>
            </a>
            <a href="suits.php" class="nav-item" id="nav-suits">
                <span class="nav-icon">👔</span>
                <span>จัดการชุด</span>
            </a>
            <a href="borrows.php" class="nav-item" id="nav-borrows">
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
            <h1>Dashboard</h1>
            <div class="header-right">
                <span class="date-display" id="current-date"></span>
            </div>
        </header>

        <main class="admin-content">
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card stat-borrowed">
                    <div class="stat-icon">👔</div>
                    <div class="stat-info">
                        <span class="stat-value" id="stat-borrowed">0</span>
                        <span class="stat-label">ยืมอยู่</span>
                    </div>
                </div>
                <div class="stat-card stat-returned">
                    <div class="stat-icon">✅</div>
                    <div class="stat-info">
                        <span class="stat-value" id="stat-returned">0</span>
                        <span class="stat-label">คืนแล้ว</span>
                    </div>
                </div>
                <div class="stat-card stat-overdue">
                    <div class="stat-icon">⚠️</div>
                    <div class="stat-info">
                        <span class="stat-value" id="stat-overdue">0</span>
                        <span class="stat-label">เกินกำหนด</span>
                    </div>
                </div>
                <div class="stat-card stat-deposit">
                    <div class="stat-icon">💰</div>
                    <div class="stat-info">
                        <span class="stat-value" id="stat-deposit">0</span>
                        <span class="stat-label">มัดจำค้างคืน</span>
                    </div>
                </div>
            </div>

            <!-- Recent Borrows -->
            <section class="admin-section">
                <div class="section-header">
                    <h2>📋 รายการยืมล่าสุด</h2>
                    <a href="borrows.php" class="btn btn-sm btn-outline">ดูทั้งหมด →</a>
                </div>
                <div class="table-wrapper">
                    <table class="data-table admin-table">
                        <thead>
                            <tr>
                                <th>รหัส</th>
                                <th>ชื่อ</th>
                                <th>ชุด</th>
                                <th>ไซส์</th>
                                <th>มัดจำ</th>
                                <th>กำหนดคืน</th>
                                <th>สถานะ</th>
                            </tr>
                        </thead>
                        <tbody id="recent-borrows-tbody"></tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <div class="toast-container" id="toast-container"></div>
    <script src="../assets/js/admin.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            loadDashboard();
        });
    </script>
</body>

</html>