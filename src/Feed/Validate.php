<?php

namespace Vigilant\Feed;

use Vigilant\Exception\FeedsException;

final class Validate
{
    /**
     * @var array<string, mixed> $details Feed details from feeds.yaml
     */
    private array $details = [];

    /**
     * Constructor
     *
     * @param array<string, mixed> $feed
     * @param int $minCheckInterval Minimum feed check interval
     */
    public function __construct(array $feed, int $minCheckInterval)
    {
        $this->details = $feed;

        $this->name();
        $this->url();
        $this->interval($minCheckInterval);
        $this->titlePrefix();

        $this->gotifyToken();
        $this->gotifyPriority();

        $this->ntfyTopic();
        $this->ntfyToken();
        $this->ntfyPriority();
    }

    /**
     * Validate entry name
     */
    private function name(): void
    {
        if (array_key_exists('name', $this->details) === false || $this->details['name'] === null) {
            throw new FeedsException('No name given for a feed');
        }
    }

    /**
     * Validate entry URL
     */
    private function url(): void
    {
        if (array_key_exists('url', $this->details) === false || $this->details['url'] === null) {
            throw new FeedsException('No url given for feed: ' . $this->details['name']);
        }
    }

    /**
     * Validate entry interval
     *
     * @param int $minCheckInterval Minimum feed check interval
     */
    private function interval(int $minCheckInterval): void
    {
        if (array_key_exists('interval', $this->details) === false || $this->details['interval'] === null) {
            throw new FeedsException('No interval given for feed: ' . $this->details['name']);
        }

        if ($this->details['interval'] < $minCheckInterval) {
            throw new FeedsException(
                'Interval is less than ' . $minCheckInterval .
                ' seconds for feed: ' . $this->details['name']
            );
        }
    }

    /**
     * Validate entry title prefix
     */
    private function titlePrefix(): void
    {
        if (array_key_exists('title_prefix', $this->details) === true && $this->details['title_prefix'] === null) {
            throw new FeedsException('Empty title prefix given for feed: ' . $this->details['name']);
        }
    }

    /**
     * Validate entry gotify token
     */
    private function gotifyToken(): void
    {
        if (array_key_exists('gotify_token', $this->details) === true && $this->details['gotify_token'] === null) {
            throw new FeedsException('Empty Gotify token given for feed: ' . $this->details['name']);
        }
    }

    /**
     * Validate entry gotify priority
     */
    private function gotifyPriority(): void
    {
        if (
            array_key_exists('gotify_priority', $this->details) === true &&
             $this->details['gotify_priority'] === null
        ) {
            throw new FeedsException('Empty Gotify priority given for feed: ' . $this->details['name']);
        }
    }

    /**
     * Validate entry ntfy topic
     */
    private function ntfyTopic(): void
    {
        if (array_key_exists('ntfy_topic', $this->details) === true && $this->details['ntfy_topic'] === null) {
            throw new FeedsException('Empty Ntfy topic given for feed: ' . $this->details['name']);
        }
    }

    /**
     * Validate entry gotify token
     */
    private function ntfyToken(): void
    {
        if (array_key_exists('ntfy_token', $this->details) === true && $this->details['ntfy_token'] === null) {
            throw new FeedsException('Empty Ntfy token given for feed: ' . $this->details['name']);
        }
    }

    /**
     * Validate entry ntfy priority
     */
    private function ntfyPriority(): void
    {
        if (
            array_key_exists('ntfy_priority', $this->details) === true &&
             $this->details['ntfy_priority'] === null
        ) {
            throw new FeedsException('Empty Ntfy priority given for feed: ' . $this->details['name']);
        }
    }
}
