<?php

declare(strict_types=1);

namespace Vigilant\Feed;

use DateTime;
use Vigilant\Helper\Time;
use Vigilant\Exception\FeedsException;

final class Validate
{
    /**
     * @var array<string, mixed> $details Feed details from feeds.yaml
     */
    private array $details = [];

    /**
     * @var array<string, mixed> $defaults Defaults feed details
     */
    private array $defaults = [
        'name' => null,
        'url' => null,
        'interval' => null,
        'truncate' => false,
        'truncate_length' => null,
    ];

    /**
     * Constructor
     *
     * @param array<string, mixed> $feed
     * @param int $minCheckInterval Minimum feed check interval
     */
    public function __construct(array $feed, int $minCheckInterval)
    {
        $this->details = array_merge($this->defaults, $feed);

        $this->name();
        $this->url();
        $this->interval($minCheckInterval);
        $this->titlePrefix();
        $this->messageTruncation();

        $this->gotifyUrl();
        $this->gotifyToken();
        $this->gotifyPriority();

        $this->ntfyUrl();
        $this->ntfyTopic();
        $this->ntfyToken();
        $this->ntfyPriority();

        $this->activeHours();
    }

    /**
     * Returns validated feed details
     * @return array<string, mixed>
     */
    public function get(): array
    {
        return $this->defaults;
    }

    /**
     * Validate entry name
     *
     * @throws FeedsException if name is not given
     */
    private function name(): void
    {
        if ($this->details['name'] === null || $this->details['name'] === '') {
            throw new FeedsException('No name given for a feed');
        }

        $this->details['name'] = (string) $this->details['name'];
    }

    /**
     * Validate entry URL
     *
     * @throws FeedsException if url is not given
     */
    private function url(): void
    {
        if ($this->details['url'] === null || $this->details['url'] === '') {
            throw new FeedsException(sprintf('No url given for feed: %s', $this->details['name']));
        }

        // todo: Add url format check
    }

    /**
     * Validate entry interval
     *
     * @param int $minCheckInterval Minimum feed check interval
     * @throws FeedsException if interval is not given
     * @throws FeedsException if interval is not a integer
     * @throws FeedsException if interval is less than minimum allowed interval
     */
    private function interval(int $minCheckInterval): void
    {
        if ($this->details['interval'] === null || $this->details['interval'] === '') {
            throw new FeedsException(sprintf('No interval given for feed: %s', $this->details['name']));
        }

        if (filter_var($this->details['interval'], FILTER_VALIDATE_INT) === false) {
            throw new FeedsException(sprintf('Non-integer interval given for feed: %s', $this->details['name']));
        }

        if ($this->details['interval'] < $minCheckInterval) {
            throw new FeedsException(sprintf(
                'Interval is less than %s seconds for feed: %s',
                $minCheckInterval,
                $this->details['name']
            ));
        }
    }

    /**
     * Validate entry title prefix
     *
     * @throws FeedsException if title prefix is empty
     */
    private function titlePrefix(): void
    {
        if (array_key_exists('title_prefix', $this->details) === true) {
            if ($this->details['title_prefix'] === null || $this->details['title_prefix'] === '') {
                throw new FeedsException(sprintf('Empty title prefix given for feed: %s', $this->details['name']));
            }

            $this->details['title_prefix'] = (string) $this->details['title_prefix'];
        }
    }

    /**
     * Validate entry message truncation
     *
     * @throws FeedsException if truncate value is invalid
     * @throws FeedsException if truncate length value is not a integer
     * @throws FeedsException if truncate length value is less than zeo
     */
    private function messageTruncation(): void
    {
        if ($this->details['truncate'] !== null) {
            $truncate = filter_var($this->details['truncate'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

            if ($truncate === null) {
                throw new FeedsException(sprintf('Invalid truncate value given for feed: %s', $this->details['name']));
            }

            $this->details['truncate'] = $truncate;
        }

        if ($this->details['truncate'] === true) {
            if ($this->details['truncate_length'] === null || $this->details['truncate_length'] === '') {
                throw new FeedsException(sprintf('No truncate length given for feed: %s', $this->details['truncate_length']));
            }

            $length = filter_var($this->details['truncate_length'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

            if ($length === null) {
                throw new FeedsException(sprintf('Invalid truncate length given for feed: %s', $this->details['name']));
            }

            if ($length < 0) {
                throw new FeedsException(sprintf(
                    'Truncate length less than zero given for feed: %s',
                    $this->details['name']
                ));
            }
        }
    }

    /**
     * Validate entry gotify URL
     *
     * @throws FeedsException if gotify url is empty
     */
    private function gotifyUrl(): void
    {
        if (array_key_exists('gotify_url', $this->details) === true) {
            if ($this->details['gotify_url'] === null || $this->details['gotify_url'] === '') {
                throw new FeedsException(sprintf('Empty Gotify url given for feed: %s', $this->details['name']));
            }

            // todo: check url format
            $this->details['gotify_url'] = (string) $this->details['gotify_url'];
        }
    }

    /**
     * Validate entry gotify token
     *
     * @throws FeedsException if gotify token is empty
     */
    private function gotifyToken(): void
    {
        if (array_key_exists('gotify_token', $this->details) === true) {
            if ($this->details['gotify_token'] === null || $this->details['gotify_token'] === '') {
                throw new FeedsException(sprintf('Empty Gotify token given for feed: %s', $this->details['name']));
            }
        }
    }

    /**
     * Validate entry gotify priority
     *
     * @throws FeedsException if gotify priority is empty
     * @throws FeedsException if gotify priority is not an integer
     */
    private function gotifyPriority(): void
    {
        if (array_key_exists('gotify_priority', $this->details) === true) {
            if ($this->details['gotify_priority'] === null || $this->details['gotify_priority'] === '') {
                throw new FeedsException(sprintf('Empty Gotify priority given for feed: %s', $this->details['name']));
            }

            if (filter_var($this->details['gotify_priority'], FILTER_VALIDATE_INT) === false) {
                throw new FeedsException(sprintf('Non-integer Gotify priority given for feed: %s', $this->details['name']));
            }
        }
    }

    /**
     * Validate entry ntfy URL
     *
     * @throws FeedsException if ntfy url is empty
     */
    private function ntfyUrl(): void
    {
        if (array_key_exists('ntfy_url', $this->details) === true) {
            if ($this->details['ntfy_url'] === null || $this->details['ntfy_url'] === '') {
                throw new FeedsException(sprintf('Empty Ntfy url given for feed: %s', $this->details['name']));
            }

            // todo: check url format
            $this->details['ntfy_url'] = (string) $this->details['ntfy_url'];
        }
    }

    /**
     * Validate entry ntfy topic
     *
     * @throws FeedsException if ntfy topic is empty
     */
    private function ntfyTopic(): void
    {
        if (array_key_exists('ntfy_topic', $this->details) === true) {
            if ($this->details['ntfy_topic'] === null || $this->details['ntfy_topic'] === '') {
                throw new FeedsException(sprintf('Empty Ntfy topic given for feed: %s', $this->details['name']));
            }
        }
    }

    /**
     * Validate entry gotify token
     *
     * @throws FeedsException if ntfy token is empty
     */
    private function ntfyToken(): void
    {
        if (array_key_exists('ntfy_token', $this->details) === true) {
            if ($this->details['ntfy_token'] === null || $this->details['ntfy_token'] === '') {
                throw new FeedsException(sprintf('Empty Ntfy token given for feed: %s', $this->details['name']));
            }
        }
    }

    /**
     * Validate entry ntfy priority
     *
     * @throws FeedsException if ntfy priority is empty
     * @throws FeedsException if ntfy priority is not an integer
     */
    private function ntfyPriority(): void
    {
        if (array_key_exists('ntfy_priority', $this->details) === true) {
            if ($this->details['ntfy_priority'] === null || $this->details['ntfy_priority'] === '') {
                throw new FeedsException(sprintf('Empty Ntfy priority given for feed: %s', $this->details['name']));
            }

            if (filter_var($this->details['ntfy_priority'], FILTER_VALIDATE_INT) === false) {
                throw new FeedsException(sprintf('Non-integer Ntfy priority given for feed: %s', $this->details['name']));
            }
        }
    }

    /**
     * Validate active hours options in entries
     *
     * @throws FeedsException if no start time is given or is empty
     * @throws FeedsException if no end time is given or is empty
     * @throws FeedsException if start time format is invalid
     * @throws FeedsException if end time format is invalid
     * @throws FeedsException if end time is before the start emd
     */
    private function activeHours(): void
    {
        if (array_key_exists('active_hours', $this->details) === true) {
            if ($this->details['active_hours'] === null) {
                throw new FeedsException(sprintf(
                    'Required active hours options not given for feed: %s',
                    $this->details['name']
                ));
            }

            if (array_key_exists('start_time', $this->details['active_hours']) === false) {
                throw new FeedsException(sprintf(
                    'No active hours start time given for feed: %s',
                    $this->details['name']
                ));
            }

            if (array_key_exists('end_time', $this->details['active_hours']) === false) {
                throw new FeedsException(sprintf(
                    'No active hours end time given for feed: %s',
                    $this->details['name']
                ));
            }

            if ($this->details['active_hours']['start_time'] === null || $this->details['active_hours']['start_time'] === '') {
                throw new FeedsException(sprintf(
                    'Empty active hours start time given for feed: %s',
                    $this->details['name']
                ));
            }

            if ($this->details['active_hours']['end_time'] === null || $this->details['active_hours']['end_time'] === '') {
                throw new FeedsException(sprintf(
                    'Empty active hours end time given for feed: %s',
                    $this->details['name']
                ));
            }

            if (Time::isValid($this->details['active_hours']['start_time']) === false) {
                throw new FeedsException(sprintf(
                    'Invalid active hours start time given for feed: %s',
                    $this->details['name']
                ));
            }

            if (Time::isValid($this->details['active_hours']['end_time']) === false) {
                throw new FeedsException(sprintf(
                    'Invalid active hours end time given for feed: %s',
                    $this->details['name']
                ));
            }

            $start = new DateTime($this->details['active_hours']['start_time']);
            $end = new DateTime($this->details['active_hours']['end_time']);

            if ($end->getTimestamp() < $start->getTimestamp()) {
                throw new FeedsException(sprintf(
                    'Active hours end time is before the start time for %s',
                    $this->details['name']
                ));
            }
        }
    }
}
