<?php
declare(strict_types=1);

use AmModa\Lib\Auth;
use AmModa\Lib\Cors;
use AmModa\Lib\GalleryOrder;

require_once __DIR__ . '/../lib/Auth.php';
require_once __DIR__ . '/../lib/Cors.php';
require_once __DIR__ . '/../lib/GalleryOrder.php';

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

$body = json_decode(file_get_contents('php://input') ?: '', true);
if (!is_array($body) || !isset($body['order']) || !is_array($body['order'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Nieprawidłowe dane.']);
    exit;
}

$pattern = '/^photo_[a-f0-9.]+\.(jpe?g|png|gif|webp)$/i';
$photosDir = __DIR__ . '/../photos';
$valid = [];
$seen = [];
foreach ($body['order'] as $id) {
    if (is_string($id)
        && !isset($seen[$id])
        && preg_match($pattern, $id)
        && is_file($photosDir . '/' . $id)
    ) {
        $valid[] = $id;
        $seen[$id] = true;
    }
}

if (!GalleryOrder::write($valid)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Nie udało się zapisać kolejności.']);
    exit;
}

echo json_encode(['ok' => true, 'order' => $valid]);
