<?php
require_once __DIR__ . '/../config.php';

function generateRefCode(): string
{
    return 'BRW-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

function formatDate(?string $date): string
{
    if (!$date)
        return '-';
    $dt = new DateTime($date);
    $thaiMonths = [
        '',
        'ม.ค.',
        'ก.พ.',
        'มี.ค.',
        'เม.ย.',
        'พ.ค.',
        'มิ.ย.',
        'ก.ค.',
        'ส.ค.',
        'ก.ย.',
        'ต.ค.',
        'พ.ย.',
        'ธ.ค.'
    ];
    $day = $dt->format('j');
    $month = $thaiMonths[(int) $dt->format('n')];
    $year = (int) $dt->format('Y') + 543;
    return "$day $month $year";
}

function formatDateTime(?string $datetime): string
{
    if (!$datetime)
        return '-';
    $dt = new DateTime($datetime);
    return formatDate($datetime) . ' ' . $dt->format('H:i');
}

function sanitize(string $input): string
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function getStatusLabel(string $status): string
{
    $labels = [
        'borrowed' => 'ยืมอยู่',
        'returned' => 'คืนแล้ว',
        'overdue' => 'เกินกำหนด',
    ];
    return $labels[$status] ?? $status;
}

function getStatusClass(string $status): string
{
    $classes = [
        'borrowed' => 'status-borrowed',
        'returned' => 'status-returned',
        'overdue' => 'status-overdue',
    ];
    return $classes[$status] ?? '';
}

function getDepositLabel(string $type, float $amount): string
{
    if ($type === 'cash') {
        return 'เงินสด ' . number_format($amount) . ' บาท';
    }
    return 'บัตรประชาชน';
}

function getConditionLabel(?string $condition): string
{
    $labels = [
        'good' => 'ดี',
        'dirty' => 'สกปรก',
        'damaged' => 'เสียหาย',
    ];
    return $labels[$condition] ?? '-';
}

function jsonResponse(array $data, int $code = 200)
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
?>