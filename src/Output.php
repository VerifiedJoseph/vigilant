<?php

namespace Vigilant;

final class Output
{
    /**
     * @var bool $quiet Suppress output status
     */
    private static bool $quiet = false;

    /**
     * Suppress text output
     */
    public static function quiet() {
        self::$quiet = true;
    }

    /**
     * Display text in terminal
     *
     * @param string $text Text string to display
     */
    public static function text(string $text = ''): void
    {
        if (self::$quiet === false) {
            echo $text . "\n";
        }
    }
}
