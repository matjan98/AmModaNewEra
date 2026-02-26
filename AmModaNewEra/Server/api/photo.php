<?php
declare(strict_types=1);

// CORS for image request (same as upload)
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
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Jeśli żądanie z parametrem img=1 – zwróć obrazek binarnie
if (isset($_GET['img']) && $_GET['img'] === '1') {
    $photosDir = __DIR__ . '/../photos';
    $mainFiles = $photosDir ? glob($photosDir . '/main.*') : false;
    $mainPath = $mainFiles && count($mainFiles) > 0 ? $mainFiles[0] : null;

    if ($mainPath === null || !is_file($mainPath)) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => 'Brak zdjęcia']);
        exit;
    }

    $mimes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
    ];
    $ext = strtolower(pathinfo($mainPath, PATHINFO_EXTENSION));
    $mime = $mimes[$ext] ?? 'application/octet-stream';
    header('Content-Type: ' . $mime);
    header('Cache-Control: no-cache');
    readfile($mainPath);
    exit;
}

// GET bez parametrów – JSON z informacją czy jest zdjęcie i URL
header('Content-Type: application/json; charset=utf-8');

$photosDir = __DIR__ . '/../photos';
$mainFiles = $photosDir ? glob($photosDir . '/main.*') : false;
$mainPath = $mainFiles && count($mainFiles) > 0 ? $mainFiles[0] : null;

if ($mainPath === null || !is_file($mainPath)) {
    echo json_encode(['ok' => true, 'hasPhoto' => false, 'url' => null]);
    exit;
}

$url = 'api/photo.php?img=1';
echo json_encode(['ok' => true, 'hasPhoto' => true, 'url' => $url]);
