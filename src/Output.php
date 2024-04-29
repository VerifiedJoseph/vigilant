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
}
