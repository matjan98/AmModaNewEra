<?php
declare(strict_types=1);

namespace AmModa\Lib;

use PDO;

final class ReviewsRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    
    public function getLatest(): ?array
    {
        $stmt = $this->pdo->query(
            'SELECT rating, rating_count, fetched_at
               FROM google_reviews
              ORDER BY fetched_at DESC
              LIMIT 1'
        );
        $row = $stmt ? $stmt->fetch() : false;
        if (!is_array($row)) {
            return null;
        }

        return [
            'rating'       => (float) $row['rating'],
            'rating_count' => (int) $row['rating_count'],
            'fetched_at'   => (string) $row['fetched_at'],
        ];
    }

    public function insertSnapshot(float $rating, int $count): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO google_reviews (rating, rating_count, fetched_at)
             VALUES (:rating, :rating_count, NOW())'
        );
        $stmt->execute([
            ':rating'       => number_format($rating, 1, '.', ''),
            ':rating_count' => $count,
        ]);
    }

    
    public function isStale(?array $row, int $ttlSeconds): bool
    {
        if ($row === null) {
            return true;
        }
        $fetchedAt = strtotime($row['fetched_at']);
        if ($fetchedAt === false) {
            return true;
        }
        return (time() - $fetchedAt) > $ttlSeconds;
    }
}
