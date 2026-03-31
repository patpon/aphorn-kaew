-- =============================================
-- ระบบยืม-คืนชุด อาภรณ์แก้ว — Database Schema
-- =============================================

CREATE DATABASE IF NOT EXISTS aphorn_kaew
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE aphorn_kaew;

-- ตาราง suits — ข้อมูลชุด
CREATE TABLE IF NOT EXISTS suits (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  suit_code   VARCHAR(20) NOT NULL UNIQUE,
  suit_name   VARCHAR(100) NOT NULL,
  description TEXT,
  image_url   VARCHAR(255),
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง suit_stock — สต็อกแยกตามไซส์
CREATE TABLE IF NOT EXISTS suit_stock (
  id       INT AUTO_INCREMENT PRIMARY KEY,
  suit_id  INT NOT NULL,
  size     ENUM('S','M','L','XL','XXL') NOT NULL,
  quantity INT DEFAULT 0,
  FOREIGN KEY (suit_id) REFERENCES suits(id) ON DELETE CASCADE,
  UNIQUE KEY unique_suit_size (suit_id, size)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง borrowers — ข้อมูลผู้ยืม
CREATE TABLE IF NOT EXISTS borrowers (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  full_name    VARCHAR(100) NOT NULL,
  phone        VARCHAR(20) NOT NULL,
  id_card      VARCHAR(20),
  created_at   DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง borrows — รายการยืม-คืน
CREATE TABLE IF NOT EXISTS borrows (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  ref_code         VARCHAR(30) NOT NULL UNIQUE,
  borrower_id      INT NOT NULL,
  suit_id          INT NOT NULL,
  size             ENUM('S','M','L','XL','XXL') NOT NULL,
  borrow_date      DATE NOT NULL,
  due_date         DATE NOT NULL,
  return_date      DATE,
  status           ENUM('borrowed','returned','overdue') DEFAULT 'borrowed',
  deposit_type     ENUM('cash','id_card') NOT NULL,
  deposit_amount   DECIMAL(10,2) DEFAULT 0.00,
  deposit_returned TINYINT(1) DEFAULT 0,
  condition_return ENUM('good','dirty','damaged'),
  note             TEXT,
  created_at       DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (borrower_id) REFERENCES borrowers(id),
  FOREIGN KEY (suit_id) REFERENCES suits(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง admins — ผู้ดูแลระบบ
CREATE TABLE IF NOT EXISTS admins (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  username     VARCHAR(50) NOT NULL UNIQUE,
  password     VARCHAR(255) NOT NULL,
  full_name    VARCHAR(100),
  created_at   DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- หมายเหตุ: ใช้ setup.php เพื่อสร้าง admin เริ่มต้น (จะ hash password ด้วย bcrypt อัตโนมัติ)
-- URL: http://your-domain/setup.php

-- =============================================
-- ข้อมูลตัวอย่าง — ชุดอาสาสมัคร
-- =============================================

INSERT INTO suits (suit_code, suit_name, description, image_url) VALUES
('A-001', 'ชุดอาสาสมัครสีขาว', 'ชุดอาสาสมัครแขนสั้น สีขาว ผ้าคอตตอน', NULL),
('A-002', 'ชุดอาสาสมัครสีฟ้า', 'ชุดอาสาสมัครแขนสั้น สีฟ้า ผ้าคอตตอน', NULL),
('A-003', 'ชุดอาสาสมัครสีเขียว', 'ชุดอาสาสมัครแขนยาว สีเขียว ผ้าไหม', NULL),
('A-004', 'ชุดอาสาสมัครสีชมพู', 'ชุดอาสาสมัครแขนสั้น สีชมพู ผ้าคอตตอน', NULL),
('A-005', 'ชุดอาสาสมัครสีม่วง', 'ชุดอาสาสมัครแขนยาว สีม่วง ผ้าไหมมัดหมี่', NULL);

-- สต็อกตัวอย่าง
INSERT INTO suit_stock (suit_id, size, quantity) VALUES
(1, 'S', 5), (1, 'M', 8), (1, 'L', 10), (1, 'XL', 6), (1, 'XXL', 3),
(2, 'S', 4), (2, 'M', 7), (2, 'L', 9), (2, 'XL', 5), (2, 'XXL', 2),
(3, 'S', 3), (3, 'M', 6), (3, 'L', 8), (3, 'XL', 4), (3, 'XXL', 2),
(4, 'S', 5), (4, 'M', 7), (4, 'L', 6), (4, 'XL', 4), (4, 'XXL', 1),
(5, 'S', 2), (5, 'M', 5), (5, 'L', 7), (5, 'XL', 3), (5, 'XXL', 1);
