<?php
/**
 * ระบบติดตั้ง อาภรณ์แก้ว
 * เปิดไฟล์นี้ครั้งเดียวเพื่อสร้าง admin เริ่มต้น
 * URL: http://your-domain/setup.php
 */
require_once 'config.php';
require_once 'includes/db.php';

$message = '';
$success = false;

try {
  $db = getDB();

  // ตรวจสอบว่ามี admin อยู่แล้วหรือไม่
  $stmt = $db->query("SELECT COUNT(*) as cnt FROM admins");
  $row = $stmt->fetch();

  if ($row['cnt'] > 0) {
    $message = '⚠️ มี Admin อยู่แล้วในระบบ ไม่จำเป็นต้อง setup อีก';
  } else {
    // สร้าง admin (password: admin1234)
    $hash = password_hash('admin1234', PASSWORD_BCRYPT);
    $stmt = $db->prepare("INSERT INTO admins (username, password, full_name) VALUES (?, ?, ?)");
    $stmt->execute(['admin', $hash, 'ผู้ดูแลระบบ']);
    $message = '✅ สร้าง Admin สำเร็จ!<br>Username: <strong>admin</strong><br>Password: <strong>admin1234</strong>';
    $success = true;
  }
} catch (Exception $e) {
  $message = '❌ เกิดข้อผิดพลาด: ' . $e->getMessage() . '<br><br>กรุณาตรวจสอบว่าได้ import ไฟล์ <code>aphorn_kaew.sql</code> ลงใน database แล้ว';
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Setup — อาภรณ์แก้ว</title>
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;600&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Prompt', sans-serif; background: linear-gradient(135deg, #667eea, #764ba2); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .card { background: white; border-radius: 16px; padding: 40px; max-width: 500px; width: 90%; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
    h1 { font-size: 1.5rem; margin-bottom: 20px; }
    .message { padding: 20px; border-radius: 12px; margin: 20px 0; line-height: 1.8; }
    .success { background: #d1fae5; color: #065f46; }
    .warning { background: #fef3c7; color: #92400e; }
    .error { background: #fee2e2; color: #991b1b; }
    a { display: inline-block; margin-top: 20px; padding: 12px 32px; background: linear-gradient(135deg, #8b4ffc, #6c1de0); color: white; border-radius: 12px; text-decoration: none; font-weight: 500; }
    a:hover { opacity: 0.9; }
    code { background: rgba(0,0,0,0.05); padding: 2px 6px; border-radius: 4px; }
  </style>
</head>
<body>
  <div class="card">
    <h1>🛠️ Setup อาภรณ์แก้ว</h1>
    <div class="message <?= $success ? 'success' : ($row['cnt'] > 0 ? 'warning' : 'error') ?>">
      <?= $message ?>
    </div>
    <a href="index.php">→ ไปหน้าหลัก</a>
    <br>
    <a href="admin/index.php" style="background: #334155;">→ ไป Admin Login</a>
    <p style="margin-top:30px; font-size:0.75rem; color:#94a3b8;">⚠️ กรุณาลบไฟล์ setup.php หลังติดตั้งเสร็จแล้ว</p>
  </div>
</body>
</html>
