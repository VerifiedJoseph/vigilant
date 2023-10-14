<?php

namespace Vigilant\Feed;

use Vigilant\Config;
use Vigilant\Feed\Validate;
use Vigilant\Feed\Details;

final class Feed
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var array<string, mixed> $feed Feed entry from feeds.yaml
     */
    private array $feed = [];

    /**
     * Constructor
     *
     * @param array<string, mixed> $feed
     * @param Config $config
     */
    public function __construct(array $feed, Config $config)
    {
        $this->feed = $feed;
        $this->config = $config;

        $this->validate();
    }

    /**
     * Get details for feed entry
     *
     * @return Details
     */
    public function getDetails(): Details
    {
        return new Details($this->feed);
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
}
