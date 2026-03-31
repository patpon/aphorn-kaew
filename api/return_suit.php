<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once '../includes/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['borrow_id']) || empty($data['condition'])) {
    jsonResponse(['error' => 'ข้อมูลไม่ครบ']);
}

if (!in_array($data['condition'], ['good', 'dirty', 'damaged'])) {
    jsonResponse(['error' => 'สภาพชุดไม่ถูกต้อง']);
}

$db = getDB();

// ตรวจสอบว่ามีรายการยืมนี้
$stmt = $db->prepare("SELECT id, suit_id, size, status FROM borrows WHERE id = ?");
$stmt->execute([$data['borrow_id']]);
$borrow = $stmt->fetch();

if (!$borrow) {
    jsonResponse(['error' => 'ไม่พบรายการยืม']);
}

if ($borrow['status'] === 'returned') {
    jsonResponse(['error' => 'รายการนี้คืนแล้ว']);
}

// อัปเดตสถานะการคืน
$stmt = $db->prepare("
  UPDATE borrows
  SET status = 'returned',
      return_date = CURDATE(),
      condition_return = ?,
      note = ?,
      deposit_returned = 1,
      updated_at = NOW()
  WHERE id = ?
");
$stmt->execute([
    $data['condition'],
    $data['note'] ?? '',
    $data['borrow_id'],
]);

// เพิ่มสต็อกกลับ
$stmt = $db->prepare("UPDATE suit_stock SET quantity = quantity + 1 WHERE suit_id = ? AND size = ?");
$stmt->execute([$borrow['suit_id'], $borrow['size']]);

jsonResponse(['success' => true, 'message' => 'บันทึกการคืนชุดสำเร็จ']);
?>