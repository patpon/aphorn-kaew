<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once '../includes/db.php';
require_once '../includes/functions.php';

$db = getDB();

// ฟิลเตอร์
$status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$where = [];
$params = [];

if ($status && in_array($status, ['borrowed', 'returned', 'overdue'])) {
    $where[] = "b.status = ?";
    $params[] = $status;
}

if ($search) {
    $where[] = "(br.full_name LIKE ? OR br.phone LIKE ? OR b.ref_code LIKE ? OR s.suit_name LIKE ?)";
    $searchTerm = "%$search%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $db->prepare("
  SELECT
    b.id, b.ref_code, b.size, b.borrow_date, b.due_date, b.return_date,
    b.status, b.deposit_type, b.deposit_amount, b.deposit_returned,
    b.condition_return, b.note, b.created_at,
    s.suit_code, s.suit_name,
    br.full_name, br.phone
  FROM borrows b
  JOIN borrowers br ON b.borrower_id = br.id
  JOIN suits s ON b.suit_id = s.id
  $whereSQL
  ORDER BY b.created_at DESC
");
$stmt->execute($params);
$borrows = $stmt->fetchAll();

// สถิติ
$stats = $db->query("
  SELECT
    COUNT(*) as total,
    SUM(status = 'borrowed') as total_borrowed,
    SUM(status = 'returned') as total_returned,
    SUM(status = 'borrowed' AND due_date < CURDATE()) as total_overdue,
    SUM(status = 'borrowed' AND deposit_returned = 0) as deposit_pending
  FROM borrows
")->fetch();

jsonResponse([
    'borrows' => $borrows,
    'stats' => $stats,
]);
?>