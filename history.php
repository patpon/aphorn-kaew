<?php session_start();
require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติ —
        <?= SITE_NAME ?>
    </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <header class="app-header">
        <div class="container">
            <div class="header-brand">
                <a href="index.php" class="back-btn" title="กลับหน้าหลัก">←</a>
                <div>
                    <h1 class="site-title">ประวัติ</h1>
                    <p class="site-subtitle">
                        <?= SITE_NAME ?>
                    </p>
                </div>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <section class="search-section glass-card">
                <h2>🔍 ค้นหาประวัติ</h2>
                <p>กรอกเบอร์โทรศัพท์เพื่อดูประวัติการยืม-คืน</p>
                <form onsubmit="searchHistory(event)" class="search-form">
                    <div class="search-input-group">
                        <input type="tel" id="history-phone" placeholder="กรอกเบอร์โทรศัพท์" pattern="[0-9]{9,10}"
                            required>
                        <button type="submit" class="btn btn-primary">ค้นหา</button>
                    </div>
                </form>
            </section>

            <section class="history-section" id="history-section" style="display:none;">
                <div class="section-title">
                    <h2>📋 ประวัติการยืม-คืน</h2>
                    <span class="badge" id="history-count">0</span>
                </div>

                <!-- Mobile: cards / Desktop: table -->
                <div class="history-table-wrapper" id="history-table-wrapper">
                    <table class="data-table" id="history-table">
                        <thead>
                            <tr>
                                <th>รหัส</th>
                                <th>ชุด</th>
                                <th>ไซส์</th>
                                <th>วันที่ยืม</th>
                                <th>กำหนดคืน</th>
                                <th>วันที่คืน</th>
                                <th>มัดจำ</th>
                                <th>สถานะ</th>
                            </tr>
                        </thead>
                        <tbody id="history-tbody"></tbody>
                    </table>
                </div>

                <!-- Mobile cards -->
                <div class="history-cards" id="history-cards"></div>
            </section>
        </div>
    </main>

    <footer class="app-footer">
        <div class="container">
            <p>&copy; 2026
                <?= SITE_NAME ?>
            </p>
        </div>
    </footer>

    <div class="toast-container" id="toast-container"></div>

    <script src="assets/js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            prefillHistoryPhone();
        });
    </script>
</body>

</html>