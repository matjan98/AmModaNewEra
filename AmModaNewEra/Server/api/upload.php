<?php
declare(strict_types=1);

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowedOrigins = [
    'http://localhost:9000',
    'http://127.0.0.1:9000',
    'http://localhost:9001',
    'http://127.0.0.1:9001',
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

$fieldName = 'photo';
if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
    $error = $_FILES[$fieldName]['error'] ?? 'no_file';
    $message = $error === UPLOAD_ERR_NO_FILE ? 'Brak pliku. Wybierz zdjęcie.' : 'Błąd wgrywania pliku.';
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $message]);
    exit;
}

$file = $_FILES[$fieldName];
$allowedTypes = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'image/webp' => 'webp',
];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!isset($allowedTypes[$mime])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Dozwolone formaty: JPG, PNG, GIF, WEBP.']);
    exit;
}

$maxSize = 5 * 1024 * 1024; // 5 MB
if ($file['size'] > $maxSize) {
    http_response_code(413);
    echo json_encode(['ok' => false, 'error' => 'Plik zbyt duży (max 5 MB).']);
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

// Remove previous main photo (any main.*)
foreach (glob($photosDir . '/main.*') as $old) {
    @unlink($old);
}

$ext = $allowedTypes[$mime];
$targetPath = $photosDir . '/main.' . $ext;

if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Zapis pliku nie powiódł się.']);
    exit;
}

// URL to fetch photo (frontend may append base)
echo json_encode([
    'ok' => true,
    'url' => 'api/photo.php',
    'message' => 'Zdjęcie zapisane.',
]);
