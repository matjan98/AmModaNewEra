<?php
declare(strict_types=1);

namespace AmModa\Lib;

use PDO;

final class PageViewsRepository
{
    private const ROW_ID = 1;

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function increment(): int
    {
        $this->pdo->exec(
            'UPDATE page_views SET view_count = view_count + 1 WHERE id = ' . self::ROW_ID
        );

        return $this->getCount();
    }

    public function getCount(): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT view_count FROM page_views WHERE id = :id'
        );
        $stmt->execute([':id' => self::ROW_ID]);
        $row = $stmt->fetch();

        if (!is_array($row)) {
            return 0;
        }

        return (int) $row['view_count'];
    }
}
