<?php session_start();
require_once '../config.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login —
        <?= SITE_NAME ?>
    </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body class="admin-body">
    <main class="admin-login-page">
        <div class="login-card glass-card">
            <div class="login-header">
                <div class="login-icon">🔐</div>
                <h2>Admin Login</h2>
                <p>
                    <?= SITE_NAME ?> — ระบบจัดการ
                </p>
            </div>
            <form id="admin-login-form" onsubmit="handleAdminLogin(event)">
                <div class="form-group">
                    <label for="admin-username">Username</label>
                    <input type="text" id="admin-username" name="username" placeholder="username" required>
                </div>
                <div class="form-group">
                    <label for="admin-password">Password</label>
                    <input type="password" id="admin-password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block" id="admin-login-btn">
                    <span>เข้าสู่ระบบ</span>
                    <span class="btn-loader" style="display:none;"></span>
                </button>
            </form>
            <div class="login-footer">
                <a href="../index.php" class="admin-link">← กลับหน้าผู้ใช้</a>
            </div>
        </div>
    </main>

    <div class="toast-container" id="toast-container"></div>
    <script src="../assets/js/admin.js"></script>
</body>

</html>