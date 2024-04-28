<?php

namespace Vigilant;

use DateTime;
use DateTimeZone;

class Logger
{
    private DateTimeZone $timezone;

    /**
     * @param string $timezone Timezone
     */
    public function __construct(string $timezone)
    {
        $this->timezone = new DateTimeZone($timezone);
    }

    /**
     * Display text in terminal
     *
     * @param string $text Text string to display
     */
    public function log(string $text): void
    {
        $date = new DateTime('now', $this->timezone);

        echo sprintf(
            '[Vigilant][%s] %s',
            $date->format('Y-m-d h:s:i'),
            $text
        );
    }
}
