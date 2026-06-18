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

$photoIdPattern = '/^photo_[a-f0-9.]+\.(jpe?g|png|gif|webp)$/i';

$ids = [];
if (isset($_POST['ids']) && is_array($_POST['ids'])) {
    $ids = $_POST['ids'];
} elseif (isset($_POST['id']) && is_string($_POST['id']) && $_POST['id'] !== '') {
    $ids = [$_POST['id']];
}

if ($ids === []) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Nieprawidłowe zdjęcie.']);
    exit;
}

$photosDir = __DIR__ . '/../photos';
$deleted = 0;
$deletedIds = [];
$failed = [];

foreach ($ids as $id) {
    if (!is_string($id) || !preg_match($photoIdPattern, $id)) {
        $failed[] = is_string($id) ? $id : '';
        continue;
    }

    $filePath = $photosDir . '/' . $id;
    if (!is_file($filePath)) {
        $failed[] = $id;
        continue;
    }

    if (unlink($filePath)) {
        $deleted++;
        $deletedIds[] = $id;
    } else {
        $failed[] = $id;
    }
}

if ($deletedIds !== []) {
    GalleryOrder::removeMany($deletedIds);
}

if ($deleted === 0) {
    http_response_code(400);
    echo json_encode([
        'ok' => false,
        'error' => 'Nie usunięto żadnego zdjęcia.',
        'failed' => $failed,
    ]);
    exit;
}

$message = $deleted === 1 ? 'Usunięto 1 zdjęcie.' : "Usunięto {$deleted} zdjęć.";
$response = [
    'ok' => true,
    'deleted' => $deleted,
    'message' => $message,
];

if ($failed !== []) {
    $response['failed'] = $failed;
}

echo json_encode($response);
