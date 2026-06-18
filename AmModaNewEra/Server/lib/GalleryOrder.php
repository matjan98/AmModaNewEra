<?php
declare(strict_types=1);

namespace AmModa\Lib;

final class GalleryOrder
{
    private const ORDER_FILENAME = 'order.json';
    private const ID_PATTERN = '/^photo_[a-f0-9.]+\.(jpe?g|png|gif|webp)$/i';

    private static function photosDir(): string
    {
        return __DIR__ . '/../photos';
    }

    private static function orderFilePath(): string
    {
        return self::photosDir() . '/' . self::ORDER_FILENAME;
    }

    /**
     * Reads the saved gallery order.
     *
     * @return list<string> Ordered photo ids (filenames).
     */
    public static function read(): array
    {
        $path = self::orderFilePath();
        if (!is_file($path)) {
            return [];
        }

        $raw = @file_get_contents($path);
        if ($raw === false || $raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return [];
        }

        $ids = [];
        foreach ($decoded as $id) {
            if (is_string($id) && preg_match(self::ID_PATTERN, $id)) {
                $ids[] = $id;
            }
        }

        return $ids;
    }

    /**
     * Persists the gallery order (deduplicated and validated).
     *
     * @param list<string> $ids
     */
    public static function write(array $ids): bool
    {
        $clean = [];
        $seen = [];
        foreach ($ids as $id) {
            if (is_string($id) && preg_match(self::ID_PATTERN, $id) && !isset($seen[$id])) {
                $clean[] = $id;
                $seen[$id] = true;
            }
        }

        $dir = self::photosDir();
        if (!is_dir($dir)) {
            return false;
        }

        $json = json_encode(array_values($clean));
        if ($json === false) {
            return false;
        }

        return @file_put_contents(self::orderFilePath(), $json, LOCK_EX) !== false;
    }

    /**
     * Removes the given ids from the saved order.
     *
     * @param list<string> $ids
     */
    public static function removeMany(array $ids): void
    {
        if ($ids === []) {
            return;
        }

        $remove = array_fill_keys(array_values($ids), true);
        $current = self::read();
        $filtered = array_values(array_filter(
            $current,
            static fn (string $id): bool => !isset($remove[$id])
        ));

        if (count($filtered) !== count($current)) {
            self::write($filtered);
        }
    }
}
