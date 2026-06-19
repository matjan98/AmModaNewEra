<?php
declare(strict_types=1);

use AmModa\Lib\Auth;
use AmModa\Lib\Cors;
use AmModa\Lib\Database;
use AmModa\Lib\PageViewsRepository;

require_once __DIR__ . '/../../lib/Auth.php';
require_once __DIR__ . '/../../lib/Cors.php';
require_once __DIR__ . '/../../lib/Database.php';
require_once __DIR__ . '/../../lib/PageViewsRepository.php';

const OLD_SITE_PAGE_VIEWS = 260104;

Cors::apply(['GET'], true);
header('Content-Type: application/json; charset=utf-8');
Cors::handlePreflight();

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method !== 'GET') {
    http_response_code(405);
    header('Allow: GET');
    echo json_encode(['ok' => false, 'error' => 'Method Not Allowed']);
    exit;
}

Auth::requireAuthenticated();

$dbConfig = require __DIR__ . '/../../config/database.php';

try {
    $pdo = Database::connect($dbConfig);
    Database::bootstrap($pdo);
} catch (Throwable $e) {
    error_log('[admin/page-views.php] DB error: ' . $e->getMessage());
    http_response_code(503);
    echo json_encode(['ok' => false, 'error' => 'Baza danych niedostepna.']);
    exit;
}

$repo = new PageViewsRepository($pdo);

echo json_encode([
    'ok'               => true,
    'oldSiteViews'     => OLD_SITE_PAGE_VIEWS,
    'currentSiteViews' => $repo->getCount(),
]);
