<?php
declare(strict_types=1);

use AmModa\Lib\Auth;
use AmModa\Lib\Cors;
use AmModa\Lib\ImageProcessor;

require_once __DIR__ . '/../lib/Auth.php';
require_once __DIR__ . '/../lib/Cors.php';
require_once __DIR__ . '/../lib/ImageProcessor.php';

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

if (!ImageProcessor::isSupported()) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Serwer nie obsługuje przetwarzania obrazów (brak GD/Imagick z WebP).']);
    exit;
}

$allowedTypes = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'image/webp' => 'webp',
    'image/bmp' => 'bmp',
    'image/x-ms-bmp' => 'bmp',
];
$maxSize = 25 * 1024 * 1024;
$maxFiles = 50;

$files = [];
if (!empty($_FILES['photo']) && is_uploaded_file($_FILES['photo']['tmp_name'] ?? '')) {
    $f = $_FILES['photo'];
    if (($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
        $files[] = ['tmp_name' => $f['tmp_name'], 'size' => $f['size']];
    }
}
if (!empty($_FILES['photos']) && is_array($_FILES['photos']['tmp_name'] ?? null)) {
    foreach ($_FILES['photos']['tmp_name'] as $i => $tmp) {
        if (is_uploaded_file($tmp) && ($_FILES['photos']['error'][$i] ?? 0) === UPLOAD_ERR_OK) {
            $files[] = [
                'tmp_name' => $tmp,
                'size' => (int) ($_FILES['photos']['size'][$i] ?? 0),
            ];
        }
    }
}

if (count($files) === 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Brak pliku. Wybierz zdjęcie.']);
    exit;
}

if (count($files) > $maxFiles) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => "Maksymalnie {$maxFiles} zdjęć na raz."]);
    exit;
}

$photosDir = __DIR__ . '/../photos';
if (!is_dir($photosDir)) {
    if (!mkdir($photosDir, 0755, true)) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'Nie można utworzyć katalogu photos.']);
        exit;
    }
}
if (!is_writable($photosDir)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Katalog photos nie jest zapisywalny.']);
    exit;
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$uploaded = [];
$errors = [];

foreach ($files as $file) {
    $tmp = $file['tmp_name'];
    $size = $file['size'];

    if ($size > $maxSize) {
        $errors[] = 'Plik zbyt duży (max 25 MB).';
        continue;
    }

    $mime = finfo_file($finfo, $tmp);
    if (!isset($allowedTypes[$mime])) {
        $errors[] = 'Dozwolone formaty: JPG, PNG, GIF, WEBP, BMP.';
        continue;
    }

    $baseName = 'photo_' . uniqid('', true);
    $targetPath = $photosDir . '/' . $baseName . '.webp';

    try {
        (new ImageProcessor())->process($tmp, $targetPath);
    } catch (\Throwable $e) {
        if (is_file($targetPath)) {
            @unlink($targetPath);
        }
        $errors[] = 'Nie udało się przetworzyć zdjęcia.';
        continue;
    }

    $uploaded[] = $baseName . '.webp';
}
finfo_close($finfo);

if (count($uploaded) === 0) {
    $message = count($errors) > 0 ? implode(' ', $errors) : 'Błąd wgrywania plików.';
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $message]);
    exit;
}

echo json_encode([
    'ok' => true,
    'uploaded' => $uploaded,
    'message' => count($uploaded) === 1 ? 'Zdjęcie zapisane.' : 'Zapisano ' . count($uploaded) . ' zdjęć.',
]);
