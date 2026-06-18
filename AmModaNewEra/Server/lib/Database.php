<?php
declare(strict_types=1);

namespace AmModa\Lib;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    public static function connect(array $config): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $config['host'],
            $config['name'],
            $config['charset'] ?? 'utf8mb4'
        );

        try {
            return new PDO($dsn, $config['user'], $config['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('Nie udalo sie polaczyc z baza danych: ' . $e->getMessage(), 0, $e);
        }
    }

    public static function bootstrap(PDO $pdo): void
    {
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS google_reviews (
                id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                rating       DECIMAL(2,1) NOT NULL,
                rating_count INT UNSIGNED NOT NULL,
                fetched_at   DATETIME NOT NULL,
                KEY ix_fetched_at (fetched_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS site_settings (
                id           TINYINT UNSIGNED NOT NULL PRIMARY KEY,
                news_text    TEXT NOT NULL,
                news_enabled TINYINT(1) NOT NULL DEFAULT 0,
                updated_at   DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS opening_hours_weekly (
                day_index TINYINT UNSIGNED NOT NULL PRIMARY KEY,
                label     VARCHAR(32) NOT NULL,
                hours     VARCHAR(64) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS opening_hours_overrides (
                override_date DATE NOT NULL PRIMARY KEY,
                hours         VARCHAR(64) NOT NULL,
                updated_at    DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );
    }
}
