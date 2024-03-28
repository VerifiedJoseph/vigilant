<?php

namespace Vigilant;

final class Output
{
    /**
     * Display text in terminal
     *
     * @param string $text Text string to display
     */
    public static function text(string $text = ''): void
    {
        echo $text . "\n";
    }

    /**
     * Output system newline character in terminal
     */
    public static function newline(): void
    {
        echo PHP_EOL;
    }
}
