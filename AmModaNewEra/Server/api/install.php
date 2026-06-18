<?php
declare(strict_types=1);

use AmModa\Lib\Database;
use AmModa\Lib\GooglePlacesClient;
use AmModa\Lib\OpeningHoursRepository;
use AmModa\Lib\ReviewsRepository;
use AmModa\Lib\SiteSettingsRepository;

require_once __DIR__ . '/../lib/Database.php';
require_once __DIR__ . '/../lib/GooglePlacesClient.php';
require_once __DIR__ . '/../lib/OpeningHoursRepository.php';
require_once __DIR__ . '/../lib/ReviewsRepository.php';
require_once __DIR__ . '/../lib/SiteSettingsRepository.php';

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
    $report['steps'][] = ['name' => 'create_tables', 'ok' => true, 'info' => 'google_reviews, site_settings, opening_hours_*'];
} catch (Throwable $e) {
    $report['steps'][] = ['name' => 'create_tables', 'ok' => false, 'error' => $e->getMessage()];
    $report['error']   = 'Nie udalo sie utworzyc tabel.';
    echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

$siteRepo = new SiteSettingsRepository($pdo);
$hoursRepo = new OpeningHoursRepository($pdo);

try {
    $siteRepo->seedDefaults(
        'Tu pojawi się krótka informacja dla klientek - np. promocja, zmiana godzin lub nowa dostawa.',
        true
    );
    $hoursRepo->seedWeeklyDefaults([
        ['day_index' => 1, 'label' => 'poniedziałek', 'hours' => '9:00 - 18:00'],
        ['day_index' => 2, 'label' => 'wtorek', 'hours' => '9:00 - 18:00'],
        ['day_index' => 3, 'label' => 'środa', 'hours' => '9:00 - 18:00'],
        ['day_index' => 4, 'label' => 'czwartek', 'hours' => '9:00 - 18:00'],
        ['day_index' => 5, 'label' => 'piątek', 'hours' => '9:00 - 18:00'],
        ['day_index' => 6, 'label' => 'sobota', 'hours' => '9:00 - 14:00'],
        ['day_index' => 0, 'label' => 'niedziela', 'hours' => 'Zamknięte'],
    ]);
    $report['steps'][] = ['name' => 'seed_site_settings', 'ok' => true];
} catch (Throwable $e) {
    $report['steps'][] = ['name' => 'seed_site_settings', 'ok' => false, 'error' => $e->getMessage()];
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
$report['hint']   = 'Setup zakonczony. Mozesz juz wywolywac api/reviews.php i api/settings.php. Plik install.php mozna usunac z serwera (opcjonalnie).';

echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
