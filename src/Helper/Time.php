<?php

namespace Vigilant\Helper;

use DateTime;

class Time
{
    /**
     * Supported time formats
     * @var array<int, string>
     */
    private static array $timeFormats = [
        'H',   // 05    - 24-hour with leading zero
        'G',   // 15    - 24-hour without leading zero
        'H:i', // 05:10 - 24-hour with leading zero and minutes
        'G:i', // 5:15 - 24-hour without leading zero and minutes
    ];

    /**
     * Check if a time format is valid
     * @param string $time
     * @return bool
     */
    public static function isValid(string $time): bool
    {
        $d = new DateTime();
        $time = $d->format('Y-m-d ') . strtolower($time);

        foreach (self::$timeFormats as $format) {
            $date = DateTime::createFromFormat('Y-m-d ' . $format, $time);

            if ($date !== false) {
                return true;
            }
        }

        return false;
    }
}
