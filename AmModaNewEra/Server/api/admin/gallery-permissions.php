<?php
declare(strict_types=1);

use AmModa\Lib\Auth;
use AmModa\Lib\Cors;

require_once __DIR__ . '/../../lib/Auth.php';
require_once __DIR__ . '/../../lib/Cors.php';

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

$photoDirMode = 0755;
$photoFileMode = 0664;
$photoIdPattern = '/^photo_[a-f0-9.]+\.(jpe?g|png|gif|webp)$/i';

$photosDir = __DIR__ . '/../../photos';

if (!is_dir($photosDir)) {
    if (!mkdir($photosDir, $photoDirMode, true)) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'Katalog photos nie istnieje i nie można go utworzyć.']);
        exit;
    }
}

@chmod($photosDir, $photoDirMode);

$fixed = 0;
$stillUnwritable = [];

foreach (glob($photosDir . '/photo_*.*') ?: [] as $path) {
    $basename = basename($path);
    if (!preg_match($photoIdPattern, $basename)) {
        continue;
    }

    if (@chmod($path, $photoFileMode)) {
        $fixed++;
    }

    if (!is_writable($path)) {
        $stillUnwritable[] = $basename;
    }
}

echo json_encode([
    'ok' => true,
    'fixed' => $fixed,
    'still_unwritable' => $stillUnwritable,
    'message' => $fixed === 0
        ? 'Brak plików do naprawy uprawnień.'
        : ($fixed === 1 ? 'Naprawiono uprawnienia 1 zdjęcia.' : "Naprawiono uprawnienia {$fixed} zdjęć."),
]);
