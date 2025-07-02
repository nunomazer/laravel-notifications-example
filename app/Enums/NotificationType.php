<?php

namespace App\Enums;

enum NotificationType: string
{
    case INFO = 'info';
    case WARNING = 'warning';
    case SUCCESS = 'success';
    case ERROR = 'error';

    /**
     * Get all enum cases as an array.
     *
     * @return array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function getLabels(): array
    {
        return [
            self::INFO->value => 'Information',
            self::SUCCESS->value => 'Success',
            self::WARNING->value => 'Warning',
            self::ERROR->value => 'Error',
        ];
    }

    /**
     * Check if a given value is a valid enum case.
     *
     * @param string $value
     * @return bool
     */
    public static function isValid(string $value): bool
    {
        return in_array($value, self::values());
    }
}
