<?php

namespace Vigilant\Feed;

final class Details
{
    /** @var array<string, mixed> $details Feed details from feeds.yaml */
    private array $details = [];

    /**
     * @param array<string, mixed> $feed
     */
    public function __construct(array $feed)
    {
        $this->details = $feed;
    }

    /**
     * Get feed hash
     *
     * @return string
     */
    public function getHash(): string
    {
        return sha1($this->details['url']);
    }

    /**
     * Get feed name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->details['name'];
    }

    /**
     * Get feed URL
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->details['url'];
    }

    /**
     * Get feed update interval
     *
     * @return int
     */
    public function getInterval(): int
    {
        return $this->details['interval'];
    }

    /**
     * Get title prefix
     *
     * @return ?string
     */
    public function getTitlePrefix(): ?string
    {
        return $this->details['title_prefix'] ?? null;
    }

    /**
     * Get gotify applications token
     *
     * @return ?string
     */
    public function getGotifyToken(): ?string
    {
        return $this->details['gotify_token'] ?? null;
    }

    /**
     * Get gotify priority
     *
     * @return ?int
     */
    public function getGotifyPriority(): ?int
    {
        return $this->details['gotify_priority'] ?? null;
    }

    /**
     * Get Ntfy auth token
     *
     * @return ?string
     */
    public function getNtfyToken(): ?string
    {
        return $this->details['ntfy_token'] ?? null;
    }

    /**
     * Get Ntfy topic
     *
     * @return ?string
     */
    public function getNtfyTopic(): ?string
    {
        return $this->details['ntfy_topic'] ?? null;
    }

    /**
     * Get Ntfy priority
     *
     * @return ?int
     */
    public function getNtfyPriority(): ?int
    {
        return $this->details['ntfy_priority'] ?? null;
    }

    /**
     * Returns active hours start time
     *
     * @return ?string
     */
    public function getActiveHoursStartTime(): ?string
    {
        return $this->details['active_hours']['start_time'] ?? null;
    }

    /**
     * Returns active hours end time
     *
     * @return ?string
     */
    public function getActiveHoursEndTime(): ?string
    {
        return $this->details['active_hours']['end_time'] ?? null;
    }

    /**
     * Returns boolean indicating if active hours are configured for the feed
     * @return bool
     */
    public function hasActiveHours(): bool
    {
        if ($this->getActiveHoursStartTime() !== null && $this->getActiveHoursEndTime() !== null) {
            return true;
        }

        return false;
    }

    /**
     * Check if entry has a ntfy token parameter
     *
     * @return bool
     */
    public function hasNtfyToken(): bool
    {
        return $this->has('ntfy_token');
    }

    /**
     * Check if entry has a ntfy topic parameter
     *
     * @return bool
     */
    public function hasNtfyTopic(): bool
    {
        return $this->has('ntfy_topic');
    }

    /**
     * Check if entry has a ntfy priority parameter
     *
     * @return bool
     */
    public function hasNtfyPriority(): bool
    {
        return $this->has('ntfy_priority');
    }

    /**
     * Check if entry has a Gotify token parameter
     *
     * @return bool
     */
    public function hasGotifyToken(): bool
    {
        return $this->has('gotify_token');
    }

    /**
     * Check if entry has a Gotify priority parameter
     *
     * @return bool
     */
    public function hasGotifyPriority(): bool
    {
        return $this->has('gotify_priority');
    }

    /**
     * heck if entry has a parameter
     *
     * @return bool
     */
    private function has(string $name): bool
    {
        if (array_key_exists($name, $this->details) === true && $this->details[$name] !== '') {
            return true;
        }

        return false;
    }
}
