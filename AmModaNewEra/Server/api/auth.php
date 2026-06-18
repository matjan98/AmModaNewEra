<?php
declare(strict_types=1);

use AmModa\Lib\Auth;
use AmModa\Lib\Cors;
use AmModa\Lib\LoginRateLimiter;

require_once __DIR__ . '/../lib/Auth.php';
require_once __DIR__ . '/../lib/Cors.php';
require_once __DIR__ . '/../lib/LoginRateLimiter.php';

Cors::apply(['GET', 'POST'], true);
header('Content-Type: application/json; charset=utf-8');
Cors::handlePreflight();

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    echo json_encode([
        'ok'            => true,
        'authenticated' => Auth::isAuthenticated(),
    ]);
    exit;
}

if ($method !== 'POST') {
    http_response_code(405);
    header('Allow: GET, POST');
    echo json_encode(['ok' => false, 'error' => 'Method Not Allowed']);
    exit;
}

$body = json_decode(file_get_contents('php://input') ?: '', true);
if (!is_array($body)) {
    $body = $_POST;
}

$action = (string) ($body['action'] ?? 'login');

if ($action === 'logout') {
    Auth::logout();
    echo json_encode(['ok' => true, 'authenticated' => false]);
    exit;
}

$username = trim((string) ($body['username'] ?? ''));
$password = (string) ($body['password'] ?? '');

if ($username === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Podaj login i hasło.']);
    exit;
}

if (LoginRateLimiter::isLimited()) {
    http_response_code(429);
    echo json_encode([
        'ok' => false,
        'error' => 'Zbyt wiele prób logowania. Spróbuj ponownie za chwilę.',
    ]);
    exit;
}

LoginRateLimiter::recordAttempt();

if (!Auth::login($username, $password)) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Nieprawidłowy login lub hasło.']);
    exit;
}

LoginRateLimiter::clearAttempts();

echo json_encode(['ok' => true, 'authenticated' => true]);
