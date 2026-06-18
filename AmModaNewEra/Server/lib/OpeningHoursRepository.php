<?php
declare(strict_types=1);

namespace AmModa\Lib;

use DateTimeImmutable;
use PDO;

final class OpeningHoursRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return list<array{day_index:int,label:string,hours:string}>
     */
    public function getWeeklyRows(): array
    {
        $stmt = $this->pdo->query(
            'SELECT day_index, label, hours
               FROM opening_hours_weekly
              ORDER BY FIELD(day_index, 1, 2, 3, 4, 5, 6, 0)'
        );

        $rows = [];
        if ($stmt) {
            while ($row = $stmt->fetch()) {
                if (!is_array($row)) {
                    continue;
                }
                $rows[] = [
                    'day_index' => (int) $row['day_index'],
                    'label'     => (string) $row['label'],
                    'hours'     => (string) $row['hours'],
                ];
            }
        }

        return $rows;
    }

    /**
     * @return array<string, string> Y-m-d => hours
     */
    public function getOverridesMap(): array
    {
        $stmt = $this->pdo->query(
            'SELECT override_date, hours FROM opening_hours_overrides ORDER BY override_date ASC'
        );

        $map = [];
        if ($stmt) {
            while ($row = $stmt->fetch()) {
                if (!is_array($row)) {
                    continue;
                }
                $map[(string) $row['override_date']] = (string) $row['hours'];
            }
        }

        return $map;
    }

    /**
     * @return list<array{override_date:string,hours:string}>
     */
    public function getOverridesList(): array
    {
        $stmt = $this->pdo->query(
            'SELECT override_date, hours
               FROM opening_hours_overrides
              ORDER BY override_date ASC'
        );

        $rows = [];
        if ($stmt) {
            while ($row = $stmt->fetch()) {
                if (!is_array($row)) {
                    continue;
                }
                $rows[] = [
                    'override_date' => (string) $row['override_date'],
                    'hours'         => (string) $row['hours'],
                ];
            }
        }

        return $rows;
    }

    /**
     * @param list<array{day_index:int,label:string,hours:string}> $rows
     */
    public function saveWeeklyRows(array $rows): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO opening_hours_weekly (day_index, label, hours)
             VALUES (:day_index, :label, :hours)
             ON DUPLICATE KEY UPDATE
                label = VALUES(label),
                hours = VALUES(hours)'
        );

        foreach ($rows as $row) {
            $stmt->execute([
                ':day_index' => (int) $row['day_index'],
                ':label'     => (string) $row['label'],
                ':hours'     => (string) $row['hours'],
            ]);
        }
    }

    public function upsertOverride(string $date, string $hours): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO opening_hours_overrides (override_date, hours, updated_at)
             VALUES (:override_date, :hours, NOW())
             ON DUPLICATE KEY UPDATE
                hours = VALUES(hours),
                updated_at = NOW()'
        );
        $stmt->execute([
            ':override_date' => $date,
            ':hours'         => $hours,
        ]);
    }

    public function deleteOverride(string $date): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM opening_hours_overrides WHERE override_date = :override_date'
        );
        $stmt->execute([':override_date' => $date]);
    }

    public function deletePastOverrides(?DateTimeImmutable $before = null): int
    {
        $before ??= new DateTimeImmutable('today');
        $stmt = $this->pdo->prepare(
            'DELETE FROM opening_hours_overrides WHERE override_date < :before_date'
        );
        $stmt->execute([':before_date' => $before->format('Y-m-d')]);

        return $stmt->rowCount();
    }

    public static function isOverrideDateAllowed(string $date, ?DateTimeImmutable $today = null): bool
    {
        $today ??= new DateTimeImmutable('today');

        return $date >= $today->format('Y-m-d');
    }

    /**
     * @param list<array{day_index:int,label:string,hours:string}> $defaultWeekly
     */
    public function seedWeeklyDefaults(array $defaultWeekly): void
    {
        if (count($this->getWeeklyRows()) > 0) {
            return;
        }

        $this->saveWeeklyRows($defaultWeekly);
    }

    /**
     * @return list<array{dayIndex:int,label:string,hours:string}>
     */
    public function getEffectiveWeeklyHours(?DateTimeImmutable $fromDate = null): array
    {
        $fromDate ??= new DateTimeImmutable('today');
        $weekly = $this->getWeeklyRows();
        $overrides = $this->getOverridesMap();
        $effective = [];

        foreach ($weekly as $row) {
            $dayIndex = (int) $row['day_index'];
            $nextDate = self::findNextDateForDayIndex($dayIndex, $fromDate);
            $dateKey = $nextDate->format('Y-m-d');
            $hours = $overrides[$dateKey] ?? $row['hours'];

            $effective[] = [
                'dayIndex' => $dayIndex,
                'label'    => $row['label'],
                'hours'    => $hours,
            ];
        }

        return $effective;
    }

    public function getTodayHours(?DateTimeImmutable $now = null): string
    {
        $now ??= new DateTimeImmutable('now');
        $dateKey = $now->format('Y-m-d');
        $overrides = $this->getOverridesMap();

        if (isset($overrides[$dateKey])) {
            return $overrides[$dateKey];
        }

        $dayIndex = (int) $now->format('w');
        foreach ($this->getWeeklyRows() as $row) {
            if ((int) $row['day_index'] === $dayIndex) {
                return $row['hours'];
            }
        }

        return 'Zamknięte';
    }

    public static function findNextDateForDayIndex(int $dayIndex, DateTimeImmutable $fromDate): DateTimeImmutable
    {
        $currentDayIndex = (int) $fromDate->format('w');
        $daysAhead = ($dayIndex - $currentDayIndex + 7) % 7;

        return $fromDate->modify('+' . $daysAhead . ' days')->setTime(0, 0, 0);
    }
}
