<?php

namespace Vigilant;

use Vigilant\Feed\Feed;
use Vigilant\Feed\Details;
use Vigilant\Exception\AppException;
use Vigilant\Exception\FeedsException;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

final class Feeds
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var Logger
     */
    private Logger $logger;

    /**
     * @var array<int, Details> $feeds Feed classes for each feeds.yaml entry
     */
    private array $feeds = [];

    /**
     * @param Config $config Config class instance
     * @param Logger $logger Logger class instance
     */
    public function __construct(Config $config, Logger $logger)
    {
        $this->config = $config;
        $this->logger = $logger;

        try {
            $feeds = $this->load($this->config->getFeedsPath());
            $this->validate($feeds);
        } catch (FeedsException $err) {
            throw new AppException($err->getMessage());
        }
    }

    /**
     * Get details of each feed in the YAML file.
     *
     * @return array<int, Details>
     */
    public function get(): array
    {
        return $this->feeds;
    }

    /**
     * Load feeds file
     *
     * @param string $path Feeds filepath
     * @return array<mixed>
     *
     * @throws FeedsException if file could not be read or the YAML is not valid.
     */
    private function load(string $path): array
    {
        try {
            $this->logger->log(sprintf('Loading feeds.yaml (%s)', $path));

            return Yaml::parseFile($path);
        } catch (ParseException $err) {
            throw new FeedsException($err->getMessage());
        }
    }

    /**
     * Validate contents of feeds.yaml
     *
     * @param array<mixed> $feeds
     * @throws FeedsException if not feeds in feeds.yaml
     */
    private function validate(array $feeds): void
    {
        $this->logger->log('Validating feeds.yaml');

        if (array_key_exists('feeds', $feeds) === false || is_array($feeds['feeds']) === false) {
            throw new FeedsException('No feeds in feeds.yaml');
        }

        foreach ($feeds['feeds'] as $entry) {
            $feed = new Feed($entry, $this->config);
            $this->feeds[] = $feed->getDetails();
        }
    }
}
