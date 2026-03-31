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

if (empty($data['username']) || empty($data['password'])) {
    jsonResponse(['error' => 'กรุณากรอก username และ password']);
}

$db = getDB();

$stmt = $db->prepare("SELECT id, username, password, full_name FROM admins WHERE username = ?");
$stmt->execute([$data['username']]);
$admin = $stmt->fetch();

if (!$admin || !password_verify($data['password'], $admin['password'])) {
    jsonResponse(['error' => 'Username หรือ Password ไม่ถูกต้อง']);
}

$_SESSION[ADMIN_SESSION_KEY] = [
    'id' => $admin['id'],
    'username' => $admin['username'],
    'full_name' => $admin['full_name'],
];

jsonResponse([
    'success' => true,
    'message' => 'เข้าสู่ระบบสำเร็จ',
    'admin' => $_SESSION[ADMIN_SESSION_KEY],
]);
?>