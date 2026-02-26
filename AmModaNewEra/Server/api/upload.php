<?php
declare(strict_types=1);

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowedOrigins = [
    'http://localhost:9000',
    'http://127.0.0.1:9000',
    'http://localhost:9001',
    'http://127.0.0.1:9001',
    'https://ammodadev.pl',
    'http://ammodadev.pl',
];
if ($origin && in_array($origin, $allowedOrigins, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Vary: Origin');
}
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo json_encode(['ok' => false, 'error' => 'Method Not Allowed']);
    exit;
}

$allowedTypes = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'image/webp' => 'webp',
];
$maxSize = 5 * 1024 * 1024; // 5 MB per file
$maxFiles = 50;

// Collect files: support single "photo" or multiple "photos[]"
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
        $errors[] = 'Plik zbyt duży (max 5 MB).';
        continue;
    }

    $mime = finfo_file($finfo, $tmp);
    if (!isset($allowedTypes[$mime])) {
        $errors[] = 'Dozwolone formaty: JPG, PNG, GIF, WEBP.';
        continue;
    }

    $ext = $allowedTypes[$mime];
    $baseName = 'photo_' . uniqid('', true);
    $targetPath = $photosDir . '/' . $baseName . '.' . $ext;

    if (!move_uploaded_file($tmp, $targetPath)) {
        $errors[] = 'Zapis pliku nie powiódł się.';
        continue;
    }

    $uploaded[] = $baseName . '.' . $ext;
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
