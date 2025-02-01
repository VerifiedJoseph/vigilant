<?php

declare(strict_types=1);

namespace Vigilant\Helper;

use DateTime;

class Time
{
    /**
     * Supported time formats
     * @var array<int, string>
     */
    private static array $timeFormats = [
        'H:i', // 05:10 - 24-hour with leading zero and minutes
        'G:i', // 5:15  - 24-hour without leading zero and minutes
    ];

    /**
     * Check if a time is valid
     * @param string $time
     * @return bool
     */
    public static function isValid(string $time): bool
    {
        try {
            foreach (self::$timeFormats as $format) {
                $date = DateTime::createFromFormat(
                    'Y-m-d ' . $format,
                    (new DateTime())->format('Y-m-d ') . strtolower($time)
                );

                if ($date !== false) {
                    new DateTime($time);
                    return true;
                }
            }
        } catch (\Exception) {
            return false;
        }

        return false;
    }
}
