<?php

namespace Vigilant;

use Vigilant\Helper\File;
use Vigilant\Helper\Json;

final class Cache
{
    /**
     * @var string $path Cache folder path
     */
    private string $path = '';

    /**
     * @var string $filename Cache filename
     */
    private string $filename = '';

    /**
     * @var array<string, mixed> $data Data from cache file
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
     * @param string $path Cache folder path
     * @param string $filename Cache filename
     */
    public function __construct(string $path, string $filename)
    {
        $this->path = $path;
        $this->filename = $filename;
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
     * Is this the first feed check
     *
     * @return bool
     */
    public function isFirstCheck(): bool
    {
        if ($this->data['first_check'] === 0) {
            return true;
        }

        return false;
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
     * @return array<int, string>
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
     * @param array<int, string> $items item hashes
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
        return $this->path . DIRECTORY_SEPARATOR . $this->filename;
    }
}
