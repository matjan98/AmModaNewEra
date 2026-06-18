<?php
declare(strict_types=1);

namespace AmModa\Lib;

/**
 * Limits admin login attempts per browser session (max 3 attempts per 20 seconds).
 */
final class LoginRateLimiter
{
    private const SESSION_KEY = 'login_attempt_times';
    private const MAX_ATTEMPTS = 3;
    private const WINDOW_SECONDS = 20;

    public static function isLimited(): bool
    {
        return count(self::recentAttemptTimes()) >= self::MAX_ATTEMPTS;
    }

    public static function recordAttempt(): void
    {
        Auth::startSession();

        $times = self::recentAttemptTimes();
        $times[] = time();
        $_SESSION[self::SESSION_KEY] = $times;
    }

    public static function clearAttempts(): void
    {
        Auth::startSession();
        unset($_SESSION[self::SESSION_KEY]);
    }

    /**
     * @return list<int>
     */
    private static function recentAttemptTimes(): array
    {
        Auth::startSession();

        $cutoff = time() - self::WINDOW_SECONDS;
        $times = $_SESSION[self::SESSION_KEY] ?? [];

        if (!is_array($times)) {
            return [];
        }

        $recent = [];
        foreach ($times as $timestamp) {
            if (is_int($timestamp) && $timestamp >= $cutoff) {
                $recent[] = $timestamp;
            }
        }

        return $recent;
    }
}
