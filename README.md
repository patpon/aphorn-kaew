อาภรณ์แก้ว — ระบบยืม-คืนชุดอาสาสมัคร
สรุปสิ่งที่สร้าง
สร้างเว็บแอพพลิเคชัน ระบบยืม-คืนชุดอาสาสมัคร ครบทุกฟีเจอร์ตาม spec — 27 ไฟล์ ที่ C:\Users\AJ\.gemini\antigravity\scratch\aphorn-kaew\

โครงสร้างโปรเจกต์
aphorn-kaew/
├── .htaccess              ← Security & deployment config
├── config.php             ← Database & app configuration
├── setup.php              ← ⭐ สร้าง admin เริ่มต้น (ใช้ครั้งเดียว)
├── aphorn_kaew.sql        ← SQL schema + sample data (5 ชุด + สต็อก)
├── index.php              ← Login + เมนูหลัก 4 ปุ่ม
├── borrow.php             ← ยืมชุด (2 ขั้นตอน)
├── return.php             ← คืนชุด + เลือกสภาพ
├── history.php            ← ประวัติการยืม-คืน
├── admin/
│   ├── index.php          ← Admin login
│   ├── dashboard.php      ← Dashboard + สถิติ
│   ├── suits.php          ← CRUD จัดการชุด
│   └── borrows.php        ← รายการยืม-คืน + Export CSV
├── api/  (10 endpoints)
│   ├── login.php, get_suits.php, create_borrow.php,
│   ├── return_suit.php, get_history.php, admin_login.php,
│   ├── admin_suits.php, admin_borrows.php, admin_export.php
├── includes/
│   ├── db.php, auth.php, functions.php
└── assets/
    ├── css/style.css      ← Premium glassmorphism design
    ├── css/admin.css      ← Admin sidebar & dashboard
    ├── js/main.js         ← User interactivity
    └── js/admin.js        ← Admin interactivity
✨ Design Features
สีม่วง-ทอง ธีมไทยพรีเมียม
Glassmorphism cards พร้อม backdrop-filter blur
Gradient backgrounds และ smooth animations
Prompt font (Google Fonts) สำหรับภาษาไทย
Mobile-first responsive — รองรับมือถือ, แท็บเล็ต, เดสก์ท็อป
🚀 ขั้นตอน Deploy ที่ hostatom.com
1. เตรียมฐานข้อมูล
เข้า cPanel ของ hostatom
สร้าง MySQL Database ชื่อ aphorn_kaew
สร้าง Database User + กำหนดสิทธิ์ All Privileges
เข้า phpMyAdmin → Import ไฟล์ aphorn_kaew.sql
2. แก้ config.php
diff
-define('DB_HOST', 'localhost');
-define('DB_USER', 'root');
-define('DB_PASS', '');
-define('DB_NAME', 'aphorn_kaew');
+define('DB_HOST', 'localhost');
+define('DB_USER', 'ชื่อuser_ที่สร้าง');
+define('DB_PASS', 'password_ที่ตั้ง');
+define('DB_NAME', 'ชื่อprefix_aphorn_kaew');
3. อัปโหลดไฟล์
อัปโหลดทุกไฟล์ไปที่ public_html/ (หรือ subdirectory ที่ต้องการ) ผ่าน File Manager หรือ FTP

4. สร้าง Admin
เปิด https://your-domain.com/setup.php → จะสร้าง admin อัตโนมัติ

Username: admin | Password: admin1234

5. ลบ setup.php
⚠️ สำคัญ! ลบ setup.php หลังสร้าง admin เสร็จเพื่อความปลอดภัย

🔐 ข้อมูล Admin เริ่มต้น
Field	Value
Username	admin
Password	admin1234
📱 ทดสอบหลัง Deploy
ทดสอบ	URL
หน้าหลัก (Login)	/index.php
Admin Login	/admin/index.php
Setup (ครั้งเดียว)	/setup.php
User Flow
Login (ชื่อ + เบอร์) → เมนูหลัก
ยืมชุด → เลือกชุด + ไซส์ → เลือกมัดจำ (เงิน/บัตร) → ยืนยัน → ได้รหัสอ้างอิง
คืนชุด → ค้นเบอร์ → เลือกรายการ → เลือกสภาพ → ยืนยัน → คืนมัดจำ
ประวัติ → ค้นเบอร์ → ดูรายการทั้งหมด
Admin Flow
Login → Dashboard (สถิติ: ยืมอยู่/คืนแล้ว/เกินกำหนด/มัดจำค้าง)
จัดการชุด → เพิ่ม/แก้ไข/ลบชุด + สต็อกแต่ละไซส์
รายการยืม-คืน → ค้นหา + กรองสถานะ + Export CSV

Comment
Ctrl+Alt+M
