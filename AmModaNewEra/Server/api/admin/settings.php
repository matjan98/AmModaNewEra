<?php
declare(strict_types=1);

use AmModa\Lib\Auth;
use AmModa\Lib\Cors;
use AmModa\Lib\Database;
use AmModa\Lib\OpeningHoursRepository;
use AmModa\Lib\SiteSettingsRepository;

require_once __DIR__ . '/../../lib/Auth.php';
require_once __DIR__ . '/../../lib/Cors.php';
require_once __DIR__ . '/../../lib/Database.php';
require_once __DIR__ . '/../../lib/SiteSettingsRepository.php';
require_once __DIR__ . '/../../lib/OpeningHoursRepository.php';

Cors::apply(['GET', 'PUT'], true);
header('Content-Type: application/json; charset=utf-8');
Cors::handlePreflight();

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if (!in_array($method, ['GET', 'PUT'], true)) {
    http_response_code(405);
    header('Allow: GET, PUT');
    echo json_encode(['ok' => false, 'error' => 'Method Not Allowed']);
    exit;
}

Auth::requireAuthenticated();

$dbConfig = require __DIR__ . '/../../config/database.php';

try {
    $pdo = Database::connect($dbConfig);
    Database::bootstrap($pdo);
} catch (Throwable $e) {
    error_log('[admin/settings.php] DB error: ' . $e->getMessage());
    http_response_code(503);
    echo json_encode(['ok' => false, 'error' => 'Baza danych niedostepna.']);
    exit;
}

$siteRepo = new SiteSettingsRepository($pdo);
$hoursRepo = new OpeningHoursRepository($pdo);

if ($method === 'GET') {
    $hoursRepo->deletePastOverrides();
    $site = $siteRepo->get();

    echo json_encode([
        'ok'              => true,
        'news'            => [
            'text'    => $site['news_text'],
            'enabled' => $site['news_enabled'],
        ],
        'weeklyHours'     => $hoursRepo->getWeeklyRows(),
        'overrides'       => $hoursRepo->getOverridesList(),
        'effectiveHours'  => $hoursRepo->getEffectiveWeeklyHours(),
    ]);
    exit;
}

$body = json_decode(file_get_contents('php://input') ?: '', true);
if (!is_array($body)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Nieprawidłowe dane JSON.']);
    exit;
}

if (isset($body['news']) && is_array($body['news'])) {
    $newsText = trim((string) ($body['news']['text'] ?? ''));
    $newsEnabled = !empty($body['news']['enabled']);
    $siteRepo->save($newsText, $newsEnabled);
}

if (isset($body['weeklyHours']) && is_array($body['weeklyHours'])) {
    $weeklyRows = [];
    foreach ($body['weeklyHours'] as $row) {
        if (!is_array($row)) {
            continue;
        }
        $dayIndex = (int) ($row['day_index'] ?? $row['dayIndex'] ?? -1);
        $label = trim((string) ($row['label'] ?? ''));
        $hours = trim((string) ($row['hours'] ?? ''));
        if ($dayIndex < 0 || $dayIndex > 6 || $label === '' || $hours === '') {
            continue;
        }
        $weeklyRows[] = [
            'day_index' => $dayIndex,
            'label'     => $label,
            'hours'     => $hours,
        ];
    }

    if (count($weeklyRows) > 0) {
        $hoursRepo->saveWeeklyRows($weeklyRows);
    }
}

if (isset($body['overrides']) && is_array($body['overrides'])) {
    foreach ($body['overrides'] as $override) {
        if (!is_array($override)) {
            continue;
        }

        $date = trim((string) ($override['override_date'] ?? $override['date'] ?? ''));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            continue;
        }

        if (!empty($override['delete'])) {
            $hoursRepo->deleteOverride($date);
            continue;
        }

        if (!OpeningHoursRepository::isOverrideDateAllowed($date)) {
            http_response_code(400);
            echo json_encode([
                'ok'    => false,
                'error' => 'Nie można ustawić wyjątku dla daty z przeszłości.',
            ]);
            exit;
        }

        $hours = trim((string) ($override['hours'] ?? ''));
        if ($hours === '') {
            continue;
        }

        $hoursRepo->upsertOverride($date, $hours);
    }
}

$hoursRepo->deletePastOverrides();
$site = $siteRepo->get();

echo json_encode([
    'ok'             => true,
    'news'           => [
        'text'    => $site['news_text'],
        'enabled' => $site['news_enabled'],
    ],
    'weeklyHours'    => $hoursRepo->getWeeklyRows(),
    'overrides'      => $hoursRepo->getOverridesList(),
    'effectiveHours' => $hoursRepo->getEffectiveWeeklyHours(),
]);
