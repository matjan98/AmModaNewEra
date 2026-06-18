<?php
declare(strict_types=1);

namespace AmModa\Lib;

final class Auth
{
    private const ADMIN_USERNAME = 'admin';
    private const ADMIN_PASSWORD = 'admin';
    private const SESSION_KEY = 'admin_authenticated';

    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }

    public static function login(string $username, string $password): bool
    {
        self::startSession();

        if ($username !== self::ADMIN_USERNAME || $password !== self::ADMIN_PASSWORD) {
            return false;
        }

        $_SESSION[self::SESSION_KEY] = true;
        return true;
    }

    public static function isAuthenticated(): bool
    {
        self::startSession();
        return !empty($_SESSION[self::SESSION_KEY]);
    }

    public static function logout(): void
    {
        self::startSession();
        unset($_SESSION[self::SESSION_KEY]);
    }

    public static function requireAuthenticated(): void
    {
        if (!self::isAuthenticated()) {
            http_response_code(401);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => false, 'error' => 'Wymagane logowanie.']);
            exit;
        }
    }
}
