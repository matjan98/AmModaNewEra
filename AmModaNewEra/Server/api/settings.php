<?php
declare(strict_types=1);

use AmModa\Lib\Cors;
use AmModa\Lib\Database;
use AmModa\Lib\OpeningHoursRepository;
use AmModa\Lib\SiteSettingsRepository;

require_once __DIR__ . '/../lib/Cors.php';
require_once __DIR__ . '/../lib/Database.php';
require_once __DIR__ . '/../lib/SiteSettingsRepository.php';
require_once __DIR__ . '/../lib/OpeningHoursRepository.php';

Cors::apply(['GET']);
header('Content-Type: application/json; charset=utf-8');
Cors::handlePreflight();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    http_response_code(405);
    header('Allow: GET');
    echo json_encode(['ok' => false, 'error' => 'Method Not Allowed']);
    exit;
}

$dbConfig = require __DIR__ . '/../config/database.php';

try {
    $pdo = Database::connect($dbConfig);
    Database::bootstrap($pdo);
} catch (Throwable $e) {
    error_log('[settings.php] DB error: ' . $e->getMessage());
    http_response_code(503);
    echo json_encode(['ok' => false, 'error' => 'Baza danych niedostepna.']);
    exit;
}

$siteRepo = new SiteSettingsRepository($pdo);
$hoursRepo = new OpeningHoursRepository($pdo);

$hoursRepo->deletePastOverrides();

$site = $siteRepo->get();
$openingHours = $hoursRepo->getEffectiveWeeklyHours();
$todayHours = $hoursRepo->getTodayHours();

echo json_encode([
    'ok'           => true,
    'news'         => [
        'text'    => $site['news_text'],
        'enabled' => $site['news_enabled'],
    ],
    'weeklyHours'  => $hoursRepo->getWeeklyRows(),
    'overrides'    => $hoursRepo->getOverridesList(),
    'openingHours' => $openingHours,
    'todayHours'   => $todayHours,
]);
