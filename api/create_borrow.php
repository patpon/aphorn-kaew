<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$data = json_decode(file_get_contents('php://input'), true);

// Validate
$required = ['full_name', 'phone', 'suit_id', 'size', 'due_date', 'deposit_type'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        jsonResponse(['error' => "กรุณากรอก $field"]);
    }
}

if (!in_array($data['deposit_type'], ['cash', 'id_card'])) {
    jsonResponse(['error' => 'ประเภทมัดจำไม่ถูกต้อง']);
}

$db = getDB();

// ตรวจสอบสต็อก
$stmt = $db->prepare("SELECT quantity FROM suit_stock WHERE suit_id = ? AND size = ?");
$stmt->execute([$data['suit_id'], $data['size']]);
$stock = $stmt->fetch();

if (!$stock || $stock['quantity'] <= 0) {
    jsonResponse(['error' => 'ชุดไซส์นี้หมดแล้ว']);
}

// บันทึกหรือดึงผู้ยืม
$stmt = $db->prepare("SELECT id FROM borrowers WHERE phone = ?");
$stmt->execute([$data['phone']]);
$borrower = $stmt->fetch();

if ($borrower) {
    $borrower_id = $borrower['id'];
    // อัปเดตชื่อ
    $stmt = $db->prepare("UPDATE borrowers SET full_name = ? WHERE id = ?");
    $stmt->execute([sanitize($data['full_name']), $borrower_id]);
} else {
    $stmt = $db->prepare("INSERT INTO borrowers (full_name, phone, id_card) VALUES (?, ?, ?)");
    $stmt->execute([sanitize($data['full_name']), $data['phone'], $data['id_card'] ?? null]);
    $borrower_id = $db->lastInsertId();
}

// สร้างรหัสอ้างอิง
$ref_code = generateRefCode();

// ตรวจสอบว่าไม่ซ้ำ
$stmt = $db->prepare("SELECT id FROM borrows WHERE ref_code = ?");
$stmt->execute([$ref_code]);
while ($stmt->fetch()) {
    $ref_code = generateRefCode();
    $stmt->execute([$ref_code]);
}

// บันทึกการยืม
$deposit_amount = $data['deposit_type'] === 'cash' ? DEPOSIT_CASH : 0;

$stmt = $db->prepare("
  INSERT INTO borrows
    (ref_code, borrower_id, suit_id, size, borrow_date, due_date,
     deposit_type, deposit_amount, status)
  VALUES (?, ?, ?, ?, CURDATE(), ?, ?, ?, 'borrowed')
");
$stmt->execute([
    $ref_code,
    $borrower_id,
    $data['suit_id'],
    $data['size'],
    $data['due_date'],
    $data['deposit_type'],
    $deposit_amount,
]);

// ลดสต็อก
$stmt = $db->prepare("UPDATE suit_stock SET quantity = quantity - 1 WHERE suit_id = ? AND size = ?");
$stmt->execute([$data['suit_id'], $data['size']]);

jsonResponse([
    'success' => true,
    'ref_code' => $ref_code,
    'message' => 'บันทึกการยืมสำเร็จ',
]);
?>