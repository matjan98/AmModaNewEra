<?php
declare(strict_types=1);

use AmModa\Lib\Database;
use AmModa\Lib\GooglePlacesClient;
use AmModa\Lib\ReviewsRepository;

require_once __DIR__ . '/../lib/Database.php';
require_once __DIR__ . '/../lib/GooglePlacesClient.php';
require_once __DIR__ . '/../lib/ReviewsRepository.php';

header('Content-Type: application/json; charset=utf-8');

$report = [
    'ok'    => false,
    'steps' => [],
];

$pdo  = null;
$repo = null;

$dbConfig     = require __DIR__ . '/../config/database.php';
$placesConfig = require __DIR__ . '/../config/google_places.php';

try {
    $pdo = Database::connect($dbConfig);
    $report['steps'][] = [
        'name' => 'connect',
        'ok'   => true,
        'info' => ['host' => $dbConfig['host'], 'database' => $dbConfig['name']],
    ];
} catch (Throwable $e) {
    $report['steps'][] = ['name' => 'connect', 'ok' => false, 'error' => $e->getMessage()];
    $report['error']   = 'Polaczenie z baza nie powiodlo sie. Sprawdz host/baze/login/haslo w Server/config/database.php oraz uprawnienia uzytkownika MySQL.';
    echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    Database::bootstrap($pdo);
    $report['steps'][] = ['name' => 'create_table', 'ok' => true, 'info' => 'CREATE TABLE IF NOT EXISTS google_reviews'];
} catch (Throwable $e) {
    $report['steps'][] = ['name' => 'create_table', 'ok' => false, 'error' => $e->getMessage()];
    $report['error']   = 'Nie udalo sie utworzyc tabeli google_reviews.';
    echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

$repo = new ReviewsRepository($pdo);

try {
    $rows = (int) $pdo->query('SELECT COUNT(*) FROM google_reviews')->fetchColumn();
    $report['steps'][] = ['name' => 'count_rows', 'ok' => true, 'info' => ['rows_before' => $rows]];
} catch (Throwable $e) {
    $report['steps'][] = ['name' => 'count_rows', 'ok' => false, 'error' => $e->getMessage()];
}

try {
    $client = new GooglePlacesClient($placesConfig);
    $data   = $client->fetchPlaceDetails();
    $report['steps'][] = ['name' => 'fetch_google', 'ok' => true, 'info' => $data];

    $repo->insertSnapshot($data['rating'], $data['userRatingCount']);
    $report['steps'][] = ['name' => 'insert_snapshot', 'ok' => true];
} catch (Throwable $e) {
    $report['steps'][] = ['name' => 'fetch_google', 'ok' => false, 'error' => $e->getMessage()];
    $report['error']   = 'Polaczenie z baza dziala, ale fetch z Google Places padl. Sprawdz klucz API / place_id w Server/config/google_places.php.';
    echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

$latest = $repo->getLatest();
$report['latest'] = $latest;
$report['ok']     = true;
$report['hint']   = 'Setup zakonczony. Mozesz juz wywolywac api/reviews.php. Plik install.php mozna usunac z serwera (opcjonalnie).';

echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
