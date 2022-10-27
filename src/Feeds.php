<?php

namespace Vigilant;

use Vigilant\Check;
use Vigilant\Feed\Feed;
use Vigilant\Exception\AppException;
use Vigilant\Exception\FeedsException;
use Vigilant\Exception\CheckException;
use Vigilant\Exception\NotificationException;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

final class Feeds
{
    /**
     * @var array $feeds Feed classes for each feeds.yaml entry
     */
    private array $feeds = [];

    /**
     * Constructor
     *
     * @param string $path Feeds filepath
     */
    public function __construct(string $path)
    {
        try {
            $feeds = $this->load($path);
            $this->validate($feeds);

        } catch(FeedsException $err) {
            throw new AppException($err->getMessage());
        }
    }

    /**
     * Check each feed for new items
     */
    public function check(): void
    {
        foreach ($this->feeds as $feed) {
            try {
                $check = new Check($feed);
                $check->run();
            } catch (CheckException | NotificationException $err) {
                Output::text($err->getMessage());
            }
        }
    }

    /**
     * Get details of each feed in the YAML file.
     *
     * @return array
     */
    public function get(): array
    {
        return $this->feeds;
    }

    /**
     * Load feeds file
     *
     * @param string $path Feeds filepath
     * @return array
     *
     * @throws FeedsException if file could not be read or the YAML is not valid.
     */
    private function load(string $path): array
    {
        try {
            Output::text('Loading feeds.yaml (' . $path . ')');

            return Yaml::parseFile($path);
        } catch (ParseException $err) {
            throw new FeedsException($err->getMessage());
        }
    }

    /**
     * Validate contents of feeds.yaml
     *
     * @throws FeedsException if not feeds in feeds.yaml
     */
    private function validate(array $feeds): void
    {
        Output::text('Validating feeds.yaml');

        if (array_key_exists('feeds', $feeds) === false || is_array($feeds['feeds']) === false) {
            throw new FeedsException('No feeds in feeds.yaml');
        }

        foreach ($feeds['feeds'] as $entry) {
            $feed = new Feed($entry);
            $this->feeds[] = $feed->getDetails();
        }
    }
}
