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
$photoFileMode = 0664;

/**
 * @return list<string>
 */
function delete_parse_ids_from_request(): array
{
    $contentType = strtolower((string) ($_SERVER['CONTENT_TYPE'] ?? ''));
    if (str_contains($contentType, 'application/json')) {
        $body = json_decode(file_get_contents('php://input') ?: '', true);
        if (is_array($body) && isset($body['ids']) && is_array($body['ids'])) {
            return $body['ids'];
        }
    }

    if (isset($_POST['ids']) && is_array($_POST['ids'])) {
        return $_POST['ids'];
    }

    if (isset($_POST['id']) && is_string($_POST['id']) && $_POST['id'] !== '') {
        return [$_POST['id']];
    }

    return [];
}

/**
 * @return array{id: string, reason: string}
 */
function delete_failed_entry(string $id, string $reason): array
{
    return ['id' => $id, 'reason' => $reason];
}

function delete_try_unlink(string $filePath, int $fileMode): bool
{
    if (unlink($filePath)) {
        return true;
    }

    @chmod($filePath, $fileMode);

    return unlink($filePath);
}

$ids = delete_parse_ids_from_request();

if ($ids === []) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Nieprawidłowe zdjęcie.']);
    exit;
}

$photosDir = __DIR__ . '/../photos';

if (!is_dir($photosDir)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Katalog photos nie istnieje.']);
    exit;
}

if (!is_writable($photosDir)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Katalog photos nie jest zapisywalny.']);
    exit;
}

$deleted = 0;
$deletedIds = [];
/** @var list<array{id: string, reason: string}> $failed */
$failed = [];

foreach ($ids as $id) {
    if (!is_string($id) || !preg_match($photoIdPattern, $id)) {
        $failed[] = delete_failed_entry(is_string($id) ? $id : '', 'invalid_id');
        continue;
    }

    $filePath = $photosDir . '/' . $id;
    if (!is_file($filePath)) {
        $failed[] = delete_failed_entry($id, 'not_found');
        continue;
    }

    if (!is_writable($filePath)) {
        @chmod($filePath, $photoFileMode);
    }

    if (delete_try_unlink($filePath, $photoFileMode)) {
        $deleted++;
        $deletedIds[] = $id;
    } else {
        $failed[] = delete_failed_entry($id, 'unlink_failed');
    }
}

if ($deletedIds !== []) {
    GalleryOrder::removeMany($deletedIds);
}

if ($deleted === 0) {
    $allUnlinkFailed = $failed !== []
        && count(array_filter($failed, static fn (array $entry): bool => $entry['reason'] === 'unlink_failed')) === count($failed);

    $error = $allUnlinkFailed
        ? 'Serwer nie może usunąć plików (uprawnienia). Skontaktuj się z administratorem hostingu lub użyj narzędzia naprawy uprawnień.'
        : 'Nie usunięto żadnego zdjęcia.';

    http_response_code(400);
    echo json_encode([
        'ok' => false,
        'error' => $error,
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
