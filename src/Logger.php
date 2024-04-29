<?php

namespace Vigilant;

use DateTime;
use DateTimeZone;

class Logger
{
    private DateTimeZone $timezone;

    /**
     * Logging level
     * 
     * - `1` - Normal
     * - `2` - Verbose
     *
     * @var int
     */
    private int $level = 1;

    /**
     * @param string $timezone Timezone
     * @param int $level Logging level (`1` - Normal, `2` - Verbose)
     */
    public function __construct(string $timezone, int $level = 1)
    {
        $this->setLevel($level);
        $this->timezone = new DateTimeZone($timezone);
    }

    /**
     * Display text in terminal
     *
     * @param string $text Text string to display
     */
    public function info(string $text): void
    {
        $this->log($text);
    }

    /**
     * Display text in terminal when debugging is enabled
     *
     * @param string $text Text string to display
     */
    public function debug(string $text): void
    {
        if ($this->level === 2) {
            $this->log($text);
        }
    }

    /**
     * Display text in terminal
     *
     * @param string $text Text string to display
     */
    private function log(string $text): void
    {
        $date = new DateTime('now', $this->timezone);

        echo sprintf(
            '[%s] %s %s',
            $date->format('Y-m-d h:i:s'),
            $text,
            PHP_EOL
        );
    }

    /**
     * Set logging level
     *
     * - `1` - Normal
     * - `2` - Verbose
     *
     * @param int $level Logging level
     */
    private function setLevel(int $level): void
    {
        if ($level < 1) {
            $this->level = 1;
        }

        if ($level > 2) {
            $this->level = 2;
        }

        $this->level = $level;
    }
}
