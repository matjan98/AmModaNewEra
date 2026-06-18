<?php
declare(strict_types=1);

namespace AmModa\Lib;

use Throwable;

/**
 * Shared Google reviews synchronization logic used by both the public
 * (api/reviews.php) and the admin (api/admin/reviews.php) endpoints.
 */
final class ReviewsSync
{
    /**
     * Refresh interval (cache TTL) in seconds, taken from the places config.
     */
    public static function ttl(array $placesConfig): int
    {
        return (int) ($placesConfig['refresh_interval_seconds'] ?? 86400);
    }

    /**
     * Reads the latest cached snapshot and whether it is stale. No Google call.
     *
     * @return array{row: array|null, stale: bool}
     */
    public static function getStatus(ReviewsRepository $repo, int $ttlSeconds): array
    {
        $row = $repo->getLatest();

        return [
            'row'   => $row,
            'stale' => $repo->isStale($row, $ttlSeconds),
        ];
    }

    /**
     * Refreshes the snapshot from Google when forced, or when auto-sync is
     * enabled and the cached snapshot is stale. Otherwise only reads the cache.
     *
     * @return array{row: array|null, stale: bool, synced: bool, error: string|null}
     */
    public static function refreshIfNeeded(
        ReviewsRepository $repo,
        GooglePlacesClient $client,
        int $ttlSeconds,
        bool $autoSyncEnabled,
        bool $force = false
    ): array {
        $row    = $repo->getLatest();
        $stale  = $repo->isStale($row, $ttlSeconds);
        $synced = false;
        $error  = null;

        if ($force || ($autoSyncEnabled && $stale)) {
            try {
                $data = $client->fetchPlaceDetails();
                $repo->insertSnapshot($data['rating'], $data['userRatingCount']);
                $row    = $repo->getLatest();
                $stale  = false;
                $synced = true;
            } catch (Throwable $e) {
                error_log('[ReviewsSync] Google Places fetch failed: ' . $e->getMessage());
                $stale = true;
                $error = $e->getMessage();
            }
        }

        return [
            'row'    => $row,
            'stale'  => $stale,
            'synced' => $synced,
            'error'  => $error,
        ];
    }
}
