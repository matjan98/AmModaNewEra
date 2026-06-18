<?php
declare(strict_types=1);

use AmModa\Lib\Auth;
use AmModa\Lib\Cors;

require_once __DIR__ . '/../lib/Auth.php';
require_once __DIR__ . '/../lib/Cors.php';

Cors::apply(['POST'], true);
header('Content-Type: application/json; charset=utf-8');
Cors::handlePreflight();

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo json_encode(['ok' => false, 'error' => 'Method Not Allowed']);
    exit;
}

Auth::requireAuthenticated();

$id = $_POST['id'] ?? '';
if ($id === '' || !preg_match('/^photo_[a-f0-9.]+\.(jpe?g|png|gif|webp)$/i', $id)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Nieprawidłowe zdjęcie.']);
    exit;
}

$photosDir = __DIR__ . '/../photos';
$filePath = $photosDir . '/' . $id;

if (!is_file($filePath)) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'error' => 'Zdjęcie nie istnieje.']);
    exit;
}

if (!unlink($filePath)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Nie udało się usunąć.']);
    exit;
}

echo json_encode(['ok' => true, 'message' => 'Usunięto.']);
