<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once '../includes/db.php';
require_once '../includes/functions.php';

$db = getDB();

$stmt = $db->query("
  SELECT s.id, s.suit_code, s.suit_name, s.description, s.image_url,
         ss.size, ss.quantity
  FROM suits s
  LEFT JOIN suit_stock ss ON s.id = ss.suit_id
  ORDER BY s.suit_code, FIELD(ss.size, 'S','M','L','XL','XXL')
");
$rows = $stmt->fetchAll();

// จัดกลุ่มตามชุด
$suits = [];
foreach ($rows as $row) {
    $id = $row['id'];
    if (!isset($suits[$id])) {
        $suits[$id] = [
            'id' => $row['id'],
            'suit_code' => $row['suit_code'],
            'suit_name' => $row['suit_name'],
            'description' => $row['description'],
            'image_url' => $row['image_url'],
            'stock' => [],
        ];
    }
    if ($row['size']) {
        $suits[$id]['stock'][] = [
            'size' => $row['size'],
            'quantity' => (int) $row['quantity'],
        ];
    }
}

jsonResponse(['suits' => array_values($suits)]);
?>