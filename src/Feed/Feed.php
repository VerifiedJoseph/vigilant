<?php

namespace Vigilant\Feed;

use Vigilant\Feed\Validate;
use Vigilant\Feed\Details;

final class Feed
{
    /**
     * @var array<string, mixed> $feed Feed entry from feeds.yaml
     */
    private array $feed = [];

    /**
     * Constructor
     *
     * @param array<string, mixed> $feed
     */
    public function __construct(array $feed)
    {
        $this->feed = $feed;
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
        new Validate($this->feed);
    }
}
