<?php session_start();
require_once '../config.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการชุด —
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
            <a href="suits.php" class="nav-item active" id="nav-suits">
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
            <h1>👔 จัดการชุด</h1>
            <div class="header-right">
                <button class="btn btn-primary" onclick="openSuitModal()">+ เพิ่มชุด</button>
            </div>
        </header>

        <main class="admin-content">
            <div class="table-wrapper">
                <table class="data-table admin-table" id="suits-table">
                    <thead>
                        <tr>
                            <th>รหัสชุด</th>
                            <th>ชื่อชุด</th>
                            <th>คำอธิบาย</th>
                            <th>S</th>
                            <th>M</th>
                            <th>L</th>
                            <th>XL</th>
                            <th>XXL</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody id="suits-tbody">
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

    <!-- Suit Modal (Add/Edit) -->
    <div class="modal-overlay" id="suit-modal" style="display:none;">
        <div class="modal-card glass-card modal-lg">
            <div class="modal-header">
                <h2 id="suit-modal-title">เพิ่มชุดใหม่</h2>
                <button class="modal-close" onclick="closeSuitModal()">✕</button>
            </div>
            <form id="suit-form" onsubmit="submitSuit(event)">
                <input type="hidden" id="suit-edit-id" name="id">
                <div class="form-row">
                    <div class="form-group">
                        <label for="suit-code">รหัสชุด</label>
                        <input type="text" id="suit-code" name="suit_code" placeholder="เช่น A-006" required>
                    </div>
                    <div class="form-group">
                        <label for="suit-name">ชื่อชุด</label>
                        <input type="text" id="suit-name" name="suit_name" placeholder="ชื่อชุด" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="suit-desc">คำอธิบาย</label>
                    <textarea id="suit-desc" name="description" rows="2" placeholder="รายละเอียดชุด..."></textarea>
                </div>
                <div class="form-group">
                    <label>สต็อกแต่ละไซส์</label>
                    <div class="stock-grid">
                        <div class="stock-item">
                            <label>S</label>
                            <input type="number" id="stock-S" min="0" value="0">
                        </div>
                        <div class="stock-item">
                            <label>M</label>
                            <input type="number" id="stock-M" min="0" value="0">
                        </div>
                        <div class="stock-item">
                            <label>L</label>
                            <input type="number" id="stock-L" min="0" value="0">
                        </div>
                        <div class="stock-item">
                            <label>XL</label>
                            <input type="number" id="stock-XL" min="0" value="0">
                        </div>
                        <div class="stock-item">
                            <label>XXL</label>
                            <input type="number" id="stock-XXL" min="0" value="0">
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeSuitModal()">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>

    <div class="toast-container" id="toast-container"></div>
    <script src="../assets/js/admin.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            loadSuitsAdmin();
        });
    </script>
</body>

</html>