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
        'g a', // 1 am  - 12-hour without leading zeros and lowercase am/pm
        'h a', // 01 am - 12-hour with leading zeros and lowercase am/pm
        'ga',  // 1am   - 12-hour without leading zeros, lowercase am/pm and no space
        'ha',  // 01am  - 12-hour with leading zeros, lowercase am/pm and no space
        'H',   // 05    - 24-hour with leading zeros
        'G',   // 15    - 24-hour without leading zeros
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

        foreach(self::$timeFormats as $format) {
            $date = DateTime::createFromFormat('Y-m-d ' . $format, $time);

            if ($date !== false) {
                return true;
            }
        }

        return false;
    }
}