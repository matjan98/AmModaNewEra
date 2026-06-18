<?php
declare(strict_types=1);

use AmModa\Lib\GalleryOrder;

require_once __DIR__ . '/../lib/GalleryOrder.php';

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

/**
 * @return array{0: string, 1: int} [etag quoted, mtime unix]
 */
function photo_cache_meta(string $filePath): array
{
    $mtime = is_file($filePath) ? (int) (@filemtime($filePath) ?: 0) : 0;
    $size = is_file($filePath) ? (int) (@filesize($filePath) ?: 0) : 0;
    $etag = '"' . $mtime . '-' . $size . '"';

    return [$etag, $mtime];
}

function photo_send_cache_headers(string $etag, int $mtime): void
{
    header('ETag: ' . $etag);
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $mtime) . ' GMT');
    header('Cache-Control: public, max-age=86400');
}

/**
 * @return bool True if client cache is valid (304 sent).
 */
function photo_try_not_modified(string $etag, int $mtime): bool
{
    photo_send_cache_headers($etag, $mtime);

    $ifNoneMatch = $_SERVER['HTTP_IF_NONE_MATCH'] ?? '';
    if ($ifNoneMatch !== '') {
        $candidates = array_map('trim', explode(',', $ifNoneMatch));
        foreach ($candidates as $candidate) {
            if ($candidate === $etag || $candidate === 'W/' . $etag) {
                http_response_code(304);
                return true;
            }
        }

        return false;
    }

    $ifModifiedSince = $_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? '';
    if ($ifModifiedSince !== '' && $mtime > 0) {
        $since = @strtotime($ifModifiedSince);
        if ($since !== false && $since >= $mtime) {
            http_response_code(304);
            return true;
        }
    }

    return false;
}


if (isset($_GET['list']) && $_GET['list'] === '1') {
    header('Content-Type: application/json; charset=utf-8');
    $photos = [];
    if (is_dir($photosDir)) {
        foreach (glob($photosDir . '/photo_*.*') ?: [] as $path) {
            $basename = basename($path);
            if (preg_match('/^photo_[a-f0-9.]+\.(jpe?g|png|gif|webp)$/i', $basename)) {
                $mtime = is_file($path) ? (int) (@filemtime($path) ?: 0) : 0;
                $photos[] = [
                    'id' => $basename,
                    'v' => $mtime,
                    'url' => 'api/photo.php?img=1&id=' . rawurlencode($basename) . '&v=' . $mtime,
                ];
            }
        }
    }

    $pos = array_flip(GalleryOrder::read());
    usort($photos, function ($a, $b) use ($pos, $photosDir) {
        $ia = $pos[$a['id']] ?? null;
        $ib = $pos[$b['id']] ?? null;
        if ($ia === null && $ib === null) {
            $tA = filemtime($photosDir . '/' . $a['id']) ?: 0;
            $tB = filemtime($photosDir . '/' . $b['id']) ?: 0;
            return $tB <=> $tA;
        }
        if ($ia === null) {
            return -1;
        }
        if ($ib === null) {
            return 1;
        }
        return $ia <=> $ib;
    });
    echo json_encode(['ok' => true, 'photos' => $photos]);
    exit;
}


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
            [$etag, $mtime] = photo_cache_meta($filePath);
            if (photo_try_not_modified($etag, $mtime)) {
                exit;
            }
            header('Content-Type: ' . $mime);
            readfile($filePath);
            exit;
        }
    }
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'Brak zdjęcia']);
    exit;
}

http_response_code(404);
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['ok' => false, 'error' => 'Nieprawidłowe żądanie']);
