<?php

namespace App\Modules\Submissions\Enums;

final class SubmissionStatus
{
    public const QUEUED = 0;
    public const RUNNING = 1;
    public const ACCEPTED = 2;
    public const WRONG_ANSWER = 3;
    public const TIME_LIMIT_EXCEEDED = 4;
    public const RUNTIME_ERROR = 5;
    public const COMPILATION_ERROR = 6;
    public const FAILED = 7;
    public const EXECUTED = 8;

    private static array $labels = [
        self::QUEUED => 'Queued',
        self::RUNNING => 'Running',
        self::ACCEPTED => 'Accepted',
        self::WRONG_ANSWER => 'Wrong Answer',
        self::TIME_LIMIT_EXCEEDED => 'Time Limit Exceeded',
        self::RUNTIME_ERROR => 'Runtime Error',
        self::COMPILATION_ERROR => 'Compilation Error',
        self::FAILED => 'Failed',
        self::EXECUTED => 'Executed',
    ];

    /**
     * Get all status integer values.
     */
    public static function values(): array
    {
        return array_keys(self::$labels);
    }

    /**
     * Check if given status integer is valid.
     */
    public static function isValid(int $status): bool
    {
        return isset(self::$labels[$status]);
    }

    /**
     * Get human-readable label for status integer.
     */
    public static function label(int $status): string
    {
        return self::$labels[$status] ?? 'Unknown';
    }
}
