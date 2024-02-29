<?php

namespace Vigilant\Feed;

final class Details
{
    /**
     * @var array<string, mixed> $details Feed details from feeds.yaml
     */
    private array $details = [];

    /**
     * Constructor
     *
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
     * Has entry got a ntfy token
     *
     * @return bool
     */
    public function hasNtfyToken(): bool
    {
        return $this->has('ntfy_token');
    }

    /**
     * Has entry got a ntfy topic
     *
     * @return bool
     */
    public function hasNtfyTopic(): bool
    {
        return $this->has('ntfy_topic');
    }

    /**
     * Has entry got a ntfy priority
     *
     * @return bool
     */
    public function hasNtfyPriority(): bool
    {
        return $this->has('ntfy_priority');
    }

    /**
     * Has entry got Gotify token
     *
     * @return bool
     */
    public function hasGotifyToken(): bool
    {
        return $this->has('gotify_token');
    }

    /**
     * Has entry got Gotify priority
     *
     * @return bool
     */
    public function hasGotifyPriority(): bool
    {
        return $this->has('gotify_priority');
    }

    /**
     * Has entry got a value
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
