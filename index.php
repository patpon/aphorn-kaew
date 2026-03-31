<?php session_start();
require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= SITE_NAME ?> — ระบบยืม-คืนชุด
    </title>
    <meta name="description" content="ระบบยืม-คืนชุดอาสาสมัคร อาภรณ์แก้ว — บริการยืมชุดแบบไม่มีค่าใช้จ่าย">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <!-- Header -->
    <header class="app-header">
        <div class="container">
            <div class="header-brand">
                <div class="logo-icon">👗</div>
                <div>
                    <h1 class="site-title">
                        <?= SITE_NAME ?>
                    </h1>
                    <p class="site-subtitle">ระบบยืม-คืนชุดอาสาสมัคร</p>
                </div>
            </div>
            <div class="header-actions" id="header-actions" style="display:none;">
                <span class="user-name" id="header-user-name"></span>
                <button class="btn btn-sm btn-outline" onclick="userLogout()">ออกจากระบบ</button>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <!-- Login Section -->
            <section class="login-section" id="login-section">
                <div class="login-card glass-card">
                    <div class="login-header">
                        <div class="login-icon">✨</div>
                        <h2>เข้าสู่ระบบ</h2>
                        <p>กรุณากรอกข้อมูลเพื่อเข้าใช้งาน</p>
                    </div>
                    <form id="login-form" onsubmit="handleLogin(event)">
                        <div class="form-group">
                            <label for="login-name">ชื่อ-นามสกุล</label>
                            <input type="text" id="login-name" name="full_name" placeholder="กรอกชื่อ-นามสกุล" required>
                        </div>
                        <div class="form-group">
                            <label for="login-phone">เบอร์โทรศัพท์</label>
                            <input type="tel" id="login-phone" name="phone" placeholder="0812345678"
                                pattern="[0-9]{9,10}" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block" id="login-btn">
                            <span>เข้าสู่ระบบ</span>
                            <span class="btn-loader" style="display:none;"></span>
                        </button>
                    </form>
                    <div class="login-footer">
                        <a href="admin/index.php" class="admin-link">🔐 เข้าสู่ระบบ Admin</a>
                    </div>
                </div>
            </section>

            <!-- Main Menu Section -->
            <section class="menu-section" id="menu-section" style="display:none;">
                <div class="welcome-banner glass-card">
                    <div class="welcome-text">
                        <h2>สวัสดี, <span id="welcome-name"></span> 👋</h2>
                        <p>เลือกเมนูที่ต้องการใช้งาน</p>
                    </div>
                </div>

                <div class="menu-grid">
                    <a href="borrow.php" class="menu-card glass-card" id="menu-borrow">
                        <div class="menu-icon">👔</div>
                        <h3>ยืมชุด</h3>
                        <p>เลือกชุด ไซส์ และวางมัดจำ</p>
                    </a>
                    <a href="return.php" class="menu-card glass-card" id="menu-return">
                        <div class="menu-icon">📦</div>
                        <h3>คืนชุด</h3>
                        <p>คืนชุดและรับมัดจำคืน</p>
                    </a>
                    <a href="history.php" class="menu-card glass-card" id="menu-history">
                        <div class="menu-icon">📋</div>
                        <h3>ประวัติ</h3>
                        <p>ดูประวัติการยืม-คืนทั้งหมด</p>
                    </a>
                    <a href="borrow.php" class="menu-card glass-card" id="menu-info">
                        <div class="menu-icon">ℹ️</div>
                        <h3>ข้อมูลชุด</h3>
                        <p>ดูรายละเอียดชุดที่มีให้ยืม</p>
                    </a>
                </div>

                <!-- Quick Stats -->
                <div class="info-banner glass-card">
                    <div class="info-item">
                        <span class="info-icon">💰</span>
                        <div>
                            <strong>ทางเลือก 1</strong>
                            <span>วางเงินสด
                                <?= DEPOSIT_CASH ?> บาท
                            </span>
                        </div>
                    </div>
                    <div class="info-divider"></div>
                    <div class="info-item">
                        <span class="info-icon">🪪</span>
                        <div>
                            <strong>ทางเลือก 2</strong>
                            <span>ฝากบัตรประชาชน</span>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer class="app-footer">
        <div class="container">
            <p>&copy; 2026
                <?= SITE_NAME ?> — บริการยืมชุดอาสาสมัครแบบไม่มีค่าใช้จ่าย
            </p>
        </div>
    </footer>

    <!-- Toast Notification -->
    <div class="toast-container" id="toast-container"></div>

    <script src="assets/js/main.js"></script>
</body>

</html>