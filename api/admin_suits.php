<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];

// GET — ดึงรายการชุดทั้งหมด
if ($method === 'GET') {
    $stmt = $db->query("
    SELECT s.*, 
      GROUP_CONCAT(CONCAT(ss.size, ':', ss.quantity) ORDER BY FIELD(ss.size,'S','M','L','XL','XXL') SEPARATOR ',') as stock_info
    FROM suits s
    LEFT JOIN suit_stock ss ON s.id = ss.suit_id
    GROUP BY s.id
    ORDER BY s.suit_code
  ");
    $suits = $stmt->fetchAll();
    jsonResponse(['suits' => $suits]);
}

// POST — เพิ่มชุดใหม่
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['suit_code']) || empty($data['suit_name'])) {
        jsonResponse(['error' => 'กรุณากรอกรหัสชุดและชื่อชุด']);
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("INSERT INTO suits (suit_code, suit_name, description, image_url) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data['suit_code'],
            sanitize($data['suit_name']),
            sanitize($data['description'] ?? ''),
            $data['image_url'] ?? null,
        ]);
        $suit_id = $db->lastInsertId();

        // เพิ่มสต็อก
        $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
        $stmt = $db->prepare("INSERT INTO suit_stock (suit_id, size, quantity) VALUES (?, ?, ?)");
        foreach ($sizes as $size) {
            $qty = (int) ($data['stock'][$size] ?? 0);
            $stmt->execute([$suit_id, $size, $qty]);
        }

        $db->commit();
        jsonResponse(['success' => true, 'id' => $suit_id, 'message' => 'เพิ่มชุดสำเร็จ']);
    } catch (Exception $e) {
        $db->rollBack();
        jsonResponse(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
    }
}

// PUT — แก้ไขชุด
if ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['id'])) {
        jsonResponse(['error' => 'ไม่ระบุ ID ชุด']);
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("UPDATE suits SET suit_code = ?, suit_name = ?, description = ?, image_url = ? WHERE id = ?");
        $stmt->execute([
            $data['suit_code'],
            sanitize($data['suit_name']),
            sanitize($data['description'] ?? ''),
            $data['image_url'] ?? null,
            $data['id'],
        ]);

        // อัปเดตสต็อก
        $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
        foreach ($sizes as $size) {
            $qty = (int) ($data['stock'][$size] ?? 0);
            $stmt = $db->prepare("
        INSERT INTO suit_stock (suit_id, size, quantity) VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE quantity = ?
      ");
            $stmt->execute([$data['id'], $size, $qty, $qty]);
        }

        $db->commit();
        jsonResponse(['success' => true, 'message' => 'แก้ไขชุดสำเร็จ']);
    } catch (Exception $e) {
        $db->rollBack();
        jsonResponse(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
    }
}

// DELETE — ลบชุด
if ($method === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['id'])) {
        jsonResponse(['error' => 'ไม่ระบุ ID ชุด']);
    }

    // ตรวจสอบว่ามีการยืมอยู่หรือไม่
    $stmt = $db->prepare("SELECT COUNT(*) as cnt FROM borrows WHERE suit_id = ? AND status = 'borrowed'");
    $stmt->execute([$data['id']]);
    $result = $stmt->fetch();

    if ($result['cnt'] > 0) {
        jsonResponse(['error' => 'ไม่สามารถลบได้ เนื่องจากชุดนี้มีการยืมอยู่']);
    }

    $stmt = $db->prepare("DELETE FROM suits WHERE id = ?");
    $stmt->execute([$data['id']]);

    jsonResponse(['success' => true, 'message' => 'ลบชุดสำเร็จ']);
}
?>