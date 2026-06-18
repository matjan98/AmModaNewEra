<?php
declare(strict_types=1);

use AmModa\Lib\Auth;
use AmModa\Lib\Cors;
use AmModa\Lib\Database;
use AmModa\Lib\GooglePlacesClient;
use AmModa\Lib\ReviewsRepository;
use AmModa\Lib\ReviewsSync;
use AmModa\Lib\SiteSettingsRepository;

require_once __DIR__ . '/../../lib/Auth.php';
require_once __DIR__ . '/../../lib/Cors.php';
require_once __DIR__ . '/../../lib/Database.php';
require_once __DIR__ . '/../../lib/GooglePlacesClient.php';
require_once __DIR__ . '/../../lib/ReviewsRepository.php';
require_once __DIR__ . '/../../lib/ReviewsSync.php';
require_once __DIR__ . '/../../lib/SiteSettingsRepository.php';

Cors::apply(['GET', 'PUT', 'POST'], true);
header('Content-Type: application/json; charset=utf-8');
Cors::handlePreflight();

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if (!in_array($method, ['GET', 'PUT', 'POST'], true)) {
    http_response_code(405);
    header('Allow: GET, PUT, POST');
    echo json_encode(['ok' => false, 'error' => 'Method Not Allowed']);
    exit;
}

Auth::requireAuthenticated();

$dbConfig     = require __DIR__ . '/../../config/database.php';
$placesConfig = require __DIR__ . '/../../config/google_places.php';

try {
    $pdo = Database::connect($dbConfig);
    Database::bootstrap($pdo);
} catch (Throwable $e) {
    error_log('[admin/reviews.php] DB error: ' . $e->getMessage());
    http_response_code(503);
    echo json_encode(['ok' => false, 'error' => 'Baza danych niedostepna.']);
    exit;
}

$settingsRepo = new SiteSettingsRepository($pdo);
$reviewsRepo  = new ReviewsRepository($pdo);
$ttl          = ReviewsSync::ttl($placesConfig);

/**
 * Emits the standard status JSON for a snapshot row + auto-sync flag.
 */
$respondStatus = static function (?array $row, bool $autoSyncEnabled, bool $stale): void {
    if ($row === null) {
        http_response_code(503);
        echo json_encode([
            'ok'              => false,
            'error'           => 'Brak danych o opiniach.',
            'autoSyncEnabled' => $autoSyncEnabled,
        ]);
        return;
    }

    echo json_encode([
        'ok'              => true,
        'autoSyncEnabled' => $autoSyncEnabled,
        'rating'          => $row['rating'],
        'ratingCount'     => $row['rating_count'],
        'updatedAt'       => $row['fetched_at'],
        'stale'           => $stale,
    ]);
};

$autoSyncEnabled = (bool) $settingsRepo->get()['google_reviews_auto_sync'];

if ($method === 'GET') {
    $status = ReviewsSync::getStatus($reviewsRepo, $ttl);
    $respondStatus($status['row'], $autoSyncEnabled, $status['stale']);
    exit;
}

if ($method === 'PUT') {
    $body = json_decode(file_get_contents('php://input') ?: '', true);
    if (!is_array($body) || !array_key_exists('autoSyncEnabled', $body)) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Nieprawidłowe dane JSON.']);
        exit;
    }

    $autoSyncEnabled = (bool) $body['autoSyncEnabled'];
    $settingsRepo->saveGoogleReviewsAutoSync($autoSyncEnabled);

    $status = ReviewsSync::getStatus($reviewsRepo, $ttl);
    $respondStatus($status['row'], $autoSyncEnabled, $status['stale']);
    exit;
}

// POST -> force synchronization with Google ("Synchronizuj teraz").
$client = new GooglePlacesClient($placesConfig);
$result = ReviewsSync::refreshIfNeeded($reviewsRepo, $client, $ttl, $autoSyncEnabled, true);

if ($result['error'] !== null) {
    http_response_code(502);
    $payload = [
        'ok'              => false,
        'error'           => 'Nie udalo sie pobrac danych z Google.',
        'autoSyncEnabled' => $autoSyncEnabled,
        'stale'           => true,
    ];
    if ($result['row'] !== null) {
        $payload['rating']      = $result['row']['rating'];
        $payload['ratingCount'] = $result['row']['rating_count'];
        $payload['updatedAt']   = $result['row']['fetched_at'];
    }
    echo json_encode($payload);
    exit;
}

$respondStatus($result['row'], $autoSyncEnabled, $result['stale']);
