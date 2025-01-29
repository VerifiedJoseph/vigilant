<?php

namespace Vigilant;

use Vigilant\Helper\File;
use Vigilant\Helper\Json;

final class Cache
{
    /** @var Config Config class instance */
    private Config $config;

    /**
     * @var string $filename Cache filename
     */
    private string $filename = '';

    /**
     * @var null|string $feedUrl Feed URL
     */
    private ?string $feedUrl = null;

    /**
     * @var int $firstCheck Unix timestamp of the first check
     */
    private int $firstCheck = 0;

    /**
     * @var int $firstCheck Unix timestamp of the next check
     */
    private int $nextCheck = 0;

    /**
     * @var int $errorCount Number of feed errors occurred sine last count reset
     */
    private int $errorCount = 0;

    /**
     * @var array<int, string> $items Item hashes
     */
    private array $items = [];

    /**
     * @var int $version Cache format version
     */
    private int $version = 0;

    /**
     * @param string $filename Cache filename
     * @param Config $config Config class instance
     */
    public function __construct(string $filename, Config $config)
    {
        $this->filename = $filename;
        $this->config = $config;

        $this->load();
    }

    /**
     * Get feed URL from cache data
     *
     * @return string|null
     */
    public function getFeedUrl(): ?string
    {
        return $this->feedUrl;
    }

    /**
     * Get first check unix timestamp
     *
     * @return int
     */
    public function getFirstCheck(): int
    {
        return $this->firstCheck;
    }

    /**
     * Is this the first feed check
     *
     * @return bool
     */
    public function isFirstCheck(): bool
    {
        if ($this->firstCheck === 0) {
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
        if (time() >= $this->nextCheck) {
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
        return $this->nextCheck;
    }

    /**
     * Get item hashes
     *
     * @return array<int, string>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Get number of feed errors
     *
     * @return int
     */
    public function getErrorCount(): int
    {
        return $this->errorCount;
    }

    /**
     * Increase feed error count
     */
    public function increaseErrorCount(): void
    {
        $this->errorCount++;
    }

    /**
     * Set feed URL
     *
     * @param string $url Feed URL
     */
    public function setFeedUrl(string $url): void
    {
        $this->feedUrl = $url;
    }

    /**
     * Update next check using interval value
     *
     * @param int $interval Interval in seconds
     */
    public function updateNextCheck(int $interval): void
    {
        $this->nextCheck = time() + $interval;
    }

    /**
     * Update item hashes
     *
     * @param array<int, string> $items item hashes
     */
    public function updateItems(array $items): void
    {
        $this->items = $items;
    }

    /**
     * Reset feed error count
     */
    public function resetErrorCount(): void
    {
        $this->errorCount = 0;
    }

    /**
     * Set first check unix timestamp
     */
    public function setFirstCheck(): void
    {
        if ($this->firstCheck === 0) {
            $this->firstCheck = time();
        }
    }

    /**
     * Load and decode cache file
     */
    private function load(): void
    {
        if (File::exists($this->getPath()) === true && filesize($this->getPath()) > 0) {
            $json = File::read($this->getPath());
            $data = Json::decode($json);
            $version = $data['version'] ?? 0;

            if ($this->hasValidVersion($version) === true) {
                $this->feedUrl = $data['feed_url'];
                $this->firstCheck = $data['first_check'];
                $this->nextCheck = $data['next_check'];
                $this->errorCount = $data['error_count'];
                $this->items = $data['items'];
                $this->version = $data['version'];
            }
        }
    }

    /**
     * Encode and save data to cache file
     */
    public function save(): void
    {
        $this->version = $this->config->getCacheFormatVersion();

        $json = Json::encode([
            'feed_url' => $this->feedUrl,
            'first_check' => $this->firstCheck,
            'next_check' => $this->nextCheck,
            'error_count' => $this->errorCount,
            'items' => $this->items,
            'version' => $this->version,
        ]);

        File::write($this->getPath(), $json);
    }

    /**
     * Get cache file path
     *
     * @return string
     */
    private function getPath(): string
    {
        return $this->config->getCachePath() . DIRECTORY_SEPARATOR . $this->filename;
    }

    /**
     * Checks if format version in cache matches current version.
     */
    private function hasValidVersion(int $version): bool
    {
        if ($version === $this->config->getCacheFormatVersion()) {
            return true;
        }

        return false;
    }
}
