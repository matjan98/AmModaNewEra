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
            'SELECT news_text, news_enabled, google_reviews_auto_sync, updated_at
               FROM site_settings
              WHERE id = 1
              LIMIT 1'
        );
        $row = $stmt ? $stmt->fetch() : false;

        if (!is_array($row)) {
            return [
                'news_text'                => '',
                'news_enabled'             => false,
                'google_reviews_auto_sync' => true,
                'updated_at'               => null,
            ];
        }

        return [
            'news_text'                => (string) $row['news_text'],
            'news_enabled'             => (bool) $row['news_enabled'],
            'google_reviews_auto_sync' => (bool) $row['google_reviews_auto_sync'],
            'updated_at'               => $row['updated_at'] !== null ? (string) $row['updated_at'] : null,
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

    /**
     * Updates only the Google reviews auto-sync flag, leaving news fields untouched.
     *
     * When the settings row does not exist yet it is inserted with updated_at = NULL,
     * so seedDefaults() can still apply default news content on first install.
     */
    public function saveGoogleReviewsAutoSync(bool $enabled): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO site_settings (id, news_text, news_enabled, google_reviews_auto_sync, updated_at)
             VALUES (1, \'\', 0, :auto_sync, NULL)
             ON DUPLICATE KEY UPDATE
                google_reviews_auto_sync = VALUES(google_reviews_auto_sync)'
        );
        $stmt->execute([
            ':auto_sync' => $enabled ? 1 : 0,
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
