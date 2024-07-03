<?php

namespace Vigilant\Feed;

use Vigilant\Helper\Time;
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
        $this->doNotDisturb();
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

    /**
     * Validate do not disturb options in entries
     * @throws FeedsException if no start_time is given
     * @throws FeedsException if no end_time is given
     * @throws FeedsException if start_time is empty
     * @throws FeedsException if end_time is empty
     * @throws FeedsException if start_time format is invalid
     * @throws FeedsException if end_time format is invalid
     */
    private function doNotDisturb(): void
    {
        if (array_key_exists('do_not_disturb', $this->details) === true) {
            if ($this->details['do_not_disturb'] === null) {
                throw new FeedsException('Required do not disturb options not given for feed: ' . $this->details['name']);
            }

            if (array_key_exists('start_time', $this->details['do_not_disturb']) === false) {
                throw new FeedsException('No do not disturb start time given for feed: ' . $this->details['name']);
            }

            if (array_key_exists('end_time', $this->details['do_not_disturb']) === false) {
                throw new FeedsException('No do not disturb end time given for feed: ' . $this->details['name']);
            }

            if ($this->details['do_not_disturb']['start_time'] === null) {
                throw new FeedsException('Empty do not disturb start time given for feed: ' . $this->details['name']);
            }

            if ($this->details['do_not_disturb']['end_time'] === null) {
                throw new FeedsException('Empty do not disturb end time given for feed: ' . $this->details['name']);
            }

            if (Time::isValid($this->details['do_not_disturb']['start_time']) === false) {
                throw new FeedsException('Invalid do not disturb start time given for feed: ' . $this->details['name']);
            }

            if (Time::isValid($this->details['do_not_disturb']['end_time']) === false) {
                throw new FeedsException('Invalid do not disturb end time given for feed: ' . $this->details['name']);
            }
        }
    }
}
