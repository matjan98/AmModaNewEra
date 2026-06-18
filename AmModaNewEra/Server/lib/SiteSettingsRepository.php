<?php
declare(strict_types=1);

namespace AmModa\Lib;

use PDO;

final class SiteSettingsRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function get(): array
    {
        $stmt = $this->pdo->query(
            'SELECT news_text, news_enabled, updated_at
               FROM site_settings
              WHERE id = 1
              LIMIT 1'
        );
        $row = $stmt ? $stmt->fetch() : false;

        if (!is_array($row)) {
            return [
                'news_text'    => '',
                'news_enabled' => false,
                'updated_at'   => null,
            ];
        }

        return [
            'news_text'    => (string) $row['news_text'],
            'news_enabled' => (bool) $row['news_enabled'],
            'updated_at'   => $row['updated_at'] !== null ? (string) $row['updated_at'] : null,
        ];
    }

    public function save(string $newsText, bool $newsEnabled): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO site_settings (id, news_text, news_enabled, updated_at)
             VALUES (1, :news_text, :news_enabled, NOW())
             ON DUPLICATE KEY UPDATE
                news_text = VALUES(news_text),
                news_enabled = VALUES(news_enabled),
                updated_at = NOW()'
        );
        $stmt->execute([
            ':news_text'    => $newsText,
            ':news_enabled' => $newsEnabled ? 1 : 0,
        ]);
    }

    public function seedDefaults(string $newsText, bool $newsEnabled): void
    {
        $existing = $this->get();
        if ($existing['updated_at'] !== null) {
            return;
        }

        $this->save($newsText, $newsEnabled);
    }
}
