<?php

namespace Vigilant;

final class FeedDetails
{
    /**
     * @var array $details Feed details from feeds.yaml
     */
    private array $details = [];

    /**
     * Constructor
     *
     * @param array $feed
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
     * @return string
     */
    public function getGotifyToken(): string
    {
		if (array_key_exists('gotify_token', $this->details) === true || $this->details['gotify_token'] !== '') {
			return $this->details['gotify_token'];
		}

        return Config::get('NOTIFICATION_GOTIFY_TOKEN');
    }

    /**
     * Get gotify priority
     *
	 * Returns priority from global config if not found in feed.yaml
	 *
     * @return string
     */
    public function getGotifyPriority(): string
    {
		if (array_key_exists('gotify_token', $this->details) === true || $this->details['gotify_token'] !== '') {
			return $this->details['gotify_token'];
		}

        return Config::get('NOTIFICATION_GOTIFY_PRIORITY');
    }
}
