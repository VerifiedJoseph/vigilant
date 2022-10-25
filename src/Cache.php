<?php

namespace Vigilant;

use Vigilant\Config;
use Vigilant\Helper\File;
use Vigilant\Helper\Json;

final class Cache
{
    /**
     * @var string $filename Cache filename
     */
    private string $filename = '';

    /**
     * @var array $data Data from cache file
     */
    private array $data = [
        'feed_url' => null,
        'first_check' => 0,
        'next_check' => 0,
        'items' => []
    ];

    /**
     * Constructor
     *
     * @param string $hash
     */
    public function __construct(string $hash)
    {
        $this->filename = $hash;
        $this->load();
    }

    /**
     * Get feed URL from cache data
     *
     * @return string|null
     */
    public function getFeedUrl(): string|null
    {
        return $this->data['feed_url'];
    }

    /**
     * Get first check unix timestamp
     *
     * @return int
     */
    public function getFirstCheck(): int
    {
        return $this->data['first_check'];
    }

    /**
     * Has the cache expired
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        if (time() >= $this->data['next_check']) {
            return true;
        }

        return false;
    }

    /**
     * Get next check unix timestamp
     *
     * @return int
     */
    public function getNextCheck(): int
    {
        return $this->data['next_check'];
    }

    /**
     * Get item hashes
     *
     * @return array
     */
    public function getItems(): array
    {
        return $this->data['items'];
    }

    /**
     * Set first check unix timestamp
     */
    public function setFirstCheck(): void
    {
        if ($this->data['first_check'] === 0) {
            $this->data['first_check'] = time();
        }
    }

    /**
     * Set feed URL
     *
     * @param string $url Feed URL
     */
    public function setFeedUrl(string $url): void
    {
        $this->data['feed_url'] = $url;
    }

    /**
     * Update next check using interval value
     *
     * @param int $interval Interval in seconds
     */
    public function updateNextCheck(int $interval): void
    {
        $this->data['next_check'] = time() + $interval;
    }

    /**
     * Update item hashes
     *
     * @param array $items item hashes
     */
    public function updateItems(array $items): void
    {
        $this->data['items'] = $items;
    }

    /**
     * Load and decode cache file
     */
    private function load(): void
    {
        if (File::exists($this->getPath()) === true) {
            $json = File::read($this->getPath());
            $this->data = Json::decode($json);
        }
    }

    /**
     * Encode and save data to cache file
     */
    public function save(): void
    {
        $json = Json::encode($this->data);
        File::write($this->getPath(), $json);
    }

    /**
     * Get cache file path
     *
     * @return string
     */
    private function getPath(): string
    {
        return Config::getCachePath() . DIRECTORY_SEPARATOR . $this->filename;
    }
}
