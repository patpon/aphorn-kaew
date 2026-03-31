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

if (empty($data['full_name']) || empty($data['phone'])) {
    jsonResponse(['error' => 'กรุณากรอกชื่อและเบอร์โทรศัพท์']);
}

$db = getDB();

// ค้นหาหรือสร้างผู้ยืม
$stmt = $db->prepare("SELECT id, full_name, phone FROM borrowers WHERE phone = ?");
$stmt->execute([$data['phone']]);
$borrower = $stmt->fetch();

if (!$borrower) {
    $stmt = $db->prepare("INSERT INTO borrowers (full_name, phone) VALUES (?, ?)");
    $stmt->execute([sanitize($data['full_name']), $data['phone']]);
    $borrower = [
        'id' => $db->lastInsertId(),
        'full_name' => $data['full_name'],
        'phone' => $data['phone'],
    ];
}

// เก็บ session
$_SESSION[USER_SESSION_KEY] = [
    'id' => $borrower['id'],
    'full_name' => $borrower['full_name'],
    'phone' => $borrower['phone'],
];

jsonResponse([
    'success' => true,
    'message' => 'เข้าสู่ระบบสำเร็จ',
    'user' => $_SESSION[USER_SESSION_KEY],
]);
?>