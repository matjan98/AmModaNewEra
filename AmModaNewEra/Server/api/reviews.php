<?php
declare(strict_types=1);

use AmModa\Lib\Database;
use AmModa\Lib\GooglePlacesClient;
use AmModa\Lib\ReviewsRepository;

require_once __DIR__ . '/../lib/Database.php';
require_once __DIR__ . '/../lib/GooglePlacesClient.php';
require_once __DIR__ . '/../lib/ReviewsRepository.php';

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
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    header('Allow: GET');
    echo json_encode(['ok' => false, 'error' => 'Method Not Allowed']);
    exit;
}

$dbConfig     = require __DIR__ . '/../config/database.php';
$placesConfig = require __DIR__ . '/../config/google_places.php';

try {
    $pdo = Database::connect($dbConfig);
    Database::bootstrap($pdo);
} catch (Throwable $e) {
    error_log('[reviews.php] DB error: ' . $e->getMessage());
    http_response_code(503);
    echo json_encode(['ok' => false, 'error' => 'Baza danych niedostepna.']);
    exit;
}

$repo   = new ReviewsRepository($pdo);
$client = new GooglePlacesClient($placesConfig);

$row    = $repo->getLatest();
$ttl    = (int) ($placesConfig['refresh_interval_seconds'] ?? 86400);
$stale  = false;

if ($repo->isStale($row, $ttl)) {
    try {
        $data = $client->fetchPlaceDetails();
        $repo->insertSnapshot($data['rating'], $data['userRatingCount']);
        $row = $repo->getLatest();
    } catch (Throwable $e) {
        error_log('[reviews.php] Google Places fetch failed: ' . $e->getMessage());
        $stale = true;
    }
}

if ($row === null) {
    http_response_code(503);
    echo json_encode(['ok' => false, 'error' => 'Brak danych o opiniach.']);
    exit;
}

echo json_encode([
    'ok'          => true,
    'rating'      => $row['rating'],
    'ratingCount' => $row['rating_count'],
    'updatedAt'   => $row['fetched_at'],
    'stale'       => $stale,
]);
