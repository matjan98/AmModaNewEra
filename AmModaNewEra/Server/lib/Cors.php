<?php
declare(strict_types=1);

namespace AmModa\Lib;

final class Cors
{
    private const ALLOWED_ORIGINS = [
        'http://localhost:9000',
        'http://127.0.0.1:9000',
        'http://localhost:9001',
        'http://127.0.0.1:9001',
        'https://ammodadev.pl',
        'http://ammodadev.pl',
    ];

    /**
     * @param list<string> $methods
     */
    public static function apply(array $methods, bool $allowCredentials = false): void
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        if ($origin !== '' && in_array($origin, self::ALLOWED_ORIGINS, true)) {
            header('Access-Control-Allow-Origin: ' . $origin);
            header('Vary: Origin');
        }

        if ($allowCredentials) {
            header('Access-Control-Allow-Credentials: true');
        }

        header('Access-Control-Allow-Methods: ' . implode(', ', $methods) . ', OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
    }

    public static function handlePreflight(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }
}
