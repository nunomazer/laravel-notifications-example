<?php

namespace App\Enums;

/**
 * Enum representing the read status of notifications
 */
enum ReadStatus: string
{
    case READ = 'read';
    case UNREAD = 'unread';

    /**
     * Get all possible values of the enum
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Check if a value is valid for this enum
     *
     * @param string $value
     * @return bool
     */
    public static function isValid(string $value): bool
    {
        return in_array($value, self::values());
    }
}
