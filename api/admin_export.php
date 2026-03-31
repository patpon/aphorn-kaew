<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

$db = getDB();

$status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$where = [];
$params = [];

if ($status && in_array($status, ['borrowed', 'returned', 'overdue'])) {
    $where[] = "b.status = ?";
    $params[] = $status;
}

if ($search) {
    $where[] = "(br.full_name LIKE ? OR br.phone LIKE ? OR b.ref_code LIKE ?)";
    $searchTerm = "%$search%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $db->prepare("
  SELECT
    b.ref_code AS 'รหัสอ้างอิง',
    br.full_name AS 'ชื่อผู้ยืม',
    br.phone AS 'เบอร์โทร',
    s.suit_code AS 'รหัสชุด',
    s.suit_name AS 'ชื่อชุด',
    b.size AS 'ไซส์',
    b.borrow_date AS 'วันที่ยืม',
    b.due_date AS 'กำหนดคืน',
    b.return_date AS 'วันที่คืน',
    CASE b.status
      WHEN 'borrowed' THEN 'ยืมอยู่'
      WHEN 'returned' THEN 'คืนแล้ว'
      WHEN 'overdue' THEN 'เกินกำหนด'
    END AS 'สถานะ',
    CASE b.deposit_type
      WHEN 'cash' THEN CONCAT('เงินสด ', b.deposit_amount, ' บาท')
      WHEN 'id_card' THEN 'บัตรประชาชน'
    END AS 'มัดจำ',
    CASE b.deposit_returned
      WHEN 1 THEN 'คืนแล้ว'
      WHEN 0 THEN 'ยังไม่คืน'
    END AS 'สถานะมัดจำ',
    CASE b.condition_return
      WHEN 'good' THEN 'ดี'
      WHEN 'dirty' THEN 'สกปรก'
      WHEN 'damaged' THEN 'เสียหาย'
      ELSE '-'
    END AS 'สภาพชุด',
    IFNULL(b.note, '') AS 'หมายเหตุ'
  FROM borrows b
  JOIN borrowers br ON b.borrower_id = br.id
  JOIN suits s ON b.suit_id = s.id
  $whereSQL
  ORDER BY b.created_at DESC
");
$stmt->execute($params);
$borrows = $stmt->fetchAll();

// ส่งเป็น CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="aphorn-kaew-export-' . date('Y-m-d') . '.csv"');

// BOM for UTF-8
echo "\xEF\xBB\xBF";

$output = fopen('php://output', 'w');

// Header row
if (count($borrows) > 0) {
    fputcsv($output, array_keys($borrows[0]));
}

foreach ($borrows as $row) {
    fputcsv($output, $row);
}

fclose($output);
exit;
?>