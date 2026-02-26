<?php
declare(strict_types=1);

// CORS for image request (same as upload)
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
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$photosDir = __DIR__ . '/../photos';

// List all photos (photo_* files only)
if (isset($_GET['list']) && $_GET['list'] === '1') {
    header('Content-Type: application/json; charset=utf-8');
    $photos = [];
    if (is_dir($photosDir)) {
        foreach (glob($photosDir . '/photo_*.*') ?: [] as $path) {
            $basename = basename($path);
            if (preg_match('/^photo_[a-f0-9.]+\.(jpe?g|png|gif|webp)$/i', $basename)) {
                $photos[] = [
                    'id' => $basename,
                    'url' => 'api/photo.php?img=1&id=' . rawurlencode($basename),
                ];
            }
        }
    }
    // Newest first (by mtime)
    usort($photos, function ($a, $b) use ($photosDir) {
        $tA = filemtime($photosDir . '/' . $a['id']) ?: 0;
        $tB = filemtime($photosDir . '/' . $b['id']) ?: 0;
        return $tB <=> $tA;
    });
    echo json_encode(['ok' => true, 'photos' => $photos]);
    exit;
}

// Serve single image by id
if (isset($_GET['img']) && $_GET['img'] === '1' && !empty($_GET['id'])) {
    $id = $_GET['id'];
    if (preg_match('/^photo_[a-f0-9.]+\.(jpe?g|png|gif|webp)$/i', $id)) {
        $filePath = $photosDir . '/' . $id;
        if (is_file($filePath)) {
            $mimes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
            ];
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $mime = $mimes[$ext] ?? 'application/octet-stream';
            header('Content-Type: ' . $mime);
            header('Cache-Control: no-cache');
            readfile($filePath);
            exit;
        }
    }
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'Brak zdjęcia']);
    exit;
}

// Legacy: single main image (img=1 without id) – main.*
if (isset($_GET['img']) && $_GET['img'] === '1') {
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

// GET without params – JSON with photos list (for backward compat: hasPhoto + url for single main)
header('Content-Type: application/json; charset=utf-8');

$mainFiles = $photosDir ? glob($photosDir . '/main.*') : false;
$mainPath = $mainFiles && count($mainFiles) > 0 ? $mainFiles[0] : null;

if ($mainPath === null || !is_file($mainPath)) {
    echo json_encode(['ok' => true, 'hasPhoto' => false, 'url' => null]);
    exit;
}

$url = 'api/photo.php?img=1';
echo json_encode(['ok' => true, 'hasPhoto' => true, 'url' => $url]);
