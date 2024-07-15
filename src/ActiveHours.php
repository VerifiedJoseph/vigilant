<?php

namespace Vigilant;

use DateTime;
use DateTimeZone;

class ActiveHours
{
    private Logger $logger;

    private DateTime $now;
    private DateTime $start;
    private DateTime $end;

    private DateTimeZone $timezone;
    private bool $enabled = false;

    private string $format = 'Y-m-d H:i:s e';

    /**
     * @param DateTime $now Current time and date
     * @param ?string $startTime Start time
     * @param ?string $endTime End time
     * @param string $timezone Timezone
     * @param Logger $logger
     */
    public function __construct(DateTime $now, ?string $startTime, ?string $endTime, string $timezone, Logger $logger)
    {
        $this->logger = $logger;

        $this->now = $now;
        $this->timezone = new DateTimeZone($timezone);

        $this->process($startTime, $endTime);
    }

    /**
     * Returns boolean indicating if time is in active hours window
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Returns start time as a readable string
     * @return string
     */
    public function getStartTime(): string
    {
        return $this->start->format($this->format);
    }

    /**
     * Returns end time as a readable string
     * @return string
     */
    public function getEndTime(): string
    {
        return $this->end->format($this->format);
    }

    /**
     * @param ?string $startTime Start time
     * @param ?string $endTime End time
     */
    private function process(?string $startTime, ?string $endTime): void
    {
        if ($startTime !== null && $endTime !== null) {
            $this->start = new DateTime($startTime, $this->timezone);
            $this->end = new DateTime($endTime, $this->timezone);

            if ($this->now >= $this->start && $this->now <= $this->end) {
                $this->enabled = true;
            }

            $this->logger->debug('Active hours details:');
            $this->logger->debug('Current time: ' . $this->now->format('Y-m-d H:i:s e'));
            $this->logger->debug('Start time:   ' . $this->start->format('Y-m-d H:i:s e'));
            $this->logger->debug('End time:     ' . $this->end->format('Y-m-d H:i:s e'));
            $this->logger->debug('Enabled: ' . ($this->enabled ? 'true' : 'false'));
        }
    }
}
