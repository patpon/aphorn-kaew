<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once '../includes/db.php';
require_once '../includes/functions.php';

$phone = $_GET['phone'] ?? '';

if (empty($phone)) {
    jsonResponse(['error' => 'กรุณาระบุเบอร์โทรศัพท์']);
}

$db = getDB();

$stmt = $db->prepare("
  SELECT
    b.id, b.ref_code, b.size, b.borrow_date, b.due_date, b.return_date,
    b.status, b.deposit_type, b.deposit_amount, b.deposit_returned,
    b.condition_return, b.note,
    s.suit_code, s.suit_name,
    br.full_name, br.phone
  FROM borrows b
  JOIN borrowers br ON b.borrower_id = br.id
  JOIN suits s ON b.suit_id = s.id
  WHERE br.phone = ?
  ORDER BY b.created_at DESC
");
$stmt->execute([$phone]);
$history = $stmt->fetchAll();

jsonResponse(['history' => $history]);
?>