<?php

namespace Vigilant\Feed;

use DateTime;
use DateTimeZone;
use Vigilant\Check;
use Vigilant\Fetch;
use Vigilant\ActiveHours;
use Vigilant\Config;
use Vigilant\Logger;
use Vigilant\Feed\Validate;
use Vigilant\Feed\Details;

final class Feed
{
    /**
     * @var Details
     */
    public Details $details;

    /**
     * @var ActiveHours
     */
    public ActiveHours $activeHours;

    /**
     * @var Check
     */
    public Check $check;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var Logger
     */
    private Logger $logger;

    /**
     * @var array<string, mixed> $feed Feed entry from feeds.yaml
     */
    private array $feed = [];

    /**
     * @param array<string, mixed> $feed
     * @param Config $config
     * @param Logger $logger
     */
    public function __construct(array $feed, Config $config, Logger $logger)
    {
        $this->feed = $feed;
        $this->config = $config;
        $this->logger = $logger;

        $this->validate();
        $this->initiate();
    }

    /**
     * Validate feed entry
     */
    private function validate(): void
    {
        new Validate(
            $this->feed,
            $this->config->getMinCheckInterval()
        );
    }

    private function initiate(): void
    {
        $this->details = new Details($this->feed);
        $this->check = new Check(
            $this->details,
            new Fetch(),
            $this->config,
            $this->logger
        );

        $now = new DateTime(
            'now',
            new DateTimeZone($this->config->getTimezone())
        );

        $this->activeHours = new ActiveHours(
            $now,
            $this->details->getActiveHoursStartTime(),
            $this->details->getActiveHoursEndTime(),
            $this->config->getTimezone(),
            $this->logger
        );
    }
}
