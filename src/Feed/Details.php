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
     * Returns token from global config if not found in feed.yaml
     *
     * @return ?string
     */
    public function getGotifyToken(): ?string
    {
        if (array_key_exists('gotify_token', $this->details) === true && $this->details['gotify_token'] !== '') {
            return $this->details['gotify_token'];
        }

        return null;
    }

    /**
     * Get gotify priority
     *
     * Returns priority from global config if not found in feed.yaml
     *
     * @return ?int
     */
    public function getGotifyPriority(): ?int
    {
        if ($this->has('gotify_priority') === true) {
            return $this->details['gotify_priority'];
        }

        return null;
    }

    /**
     * Get Ntfy topic
     *
     * Returns topic from global config if not found in feed.yaml
     *
     * @return ?string
     */
    public function getNtfyTopic(): ?string
    {
        if ($this->has('ntfy_topic') === true) {
            return $this->details['ntfy_topic'];
        }

        return null;
    }

    /**
     * Get Ntfy priority
     *
     * Returns priority from global config if not found in feed.yaml
     *
     * @return ?int
     */
    public function getNtfyPriority(): ?int
    {
        if ($this->has('ntfy_priority') === true) {
            return $this->details['ntfy_priority'];
        }

        return null;
    }

    /**
     * Has the entry got a value
     *
     * @return bool
     */
    private function has(string $name): bool
    {
        return array_key_exists($name, $this->details);
    }
}
