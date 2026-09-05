<?php

declare(strict_types=1);

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
     * @var int $lastCheck Unix timestamp of the last check
     */
    private int $lastCheck = 0;

    /**
     * @var int $nextCheck Unix timestamp of the next check
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
     * Returns unix timestamp for the first check
     *
     * @return int
     */
    public function getFirstCheck(): int
    {
        return $this->firstCheck;
    }

    /**
     * Returns boolean indicating if this is the first check if a feed
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
     * Returns unix timestamp for the last check
     *
     * @return int
     */
    public function getLastCheck(): int
    {
        return $this->lastCheck;
    }

    /**
     * Returns unix timestamp for the next check
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
     * Returns boolean indicating if hash is in the cache
     *
     * @param string $hash Feed item hash
     * @return bool
     */
    public function hasItem(string $hash): bool
    {
        return in_array($hash, $this->items, true);
    }

    /**
     * Returns number of feed errors
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
     * Set last check Unix timestamp
     */
    public function setLastCheck(): void
    {
        $this->lastCheck = $this->getTimestamp();
    }

    /**
     * Update next check using interval value
     *
     * @param int $interval Interval in seconds
     */
    public function updateNextCheck(int $interval): void
    {
        $this->nextCheck = $this->getTimestamp($interval);
    }

    /**
     * Add item hash
     *
     * @param string $hash item hash
     */
    public function addItem(string $hash): void
    {
        if ($this->hasItem($hash) === false) {
            $this->items[] = $hash;
        }
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
            $this->firstCheck = $this->getTimestamp();
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
                $this->lastCheck = $data['last_check'] ?? 0;
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
            'last_check' => $this->lastCheck,
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

    /**
     * Returns unix timestamp with time to the current minute (zero seconds)
     * @param int $interval Add date interval in seconds to add to the time
     * @return int
     */
    private function getTimestamp(int $interval = 0): int
    {
        $date = new \DateTime();

        if ($interval > 0) {
            $date->add(\DateInterval::createFromDateString($interval . ' seconds'));
        }

        $date->setTime(
            (int) $date->format('H'),
            (int) $date->format('i'),
            0
        );

        return $date->getTimestamp();
    }
}
