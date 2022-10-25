<?php

namespace Vigilant;

use Vigilant\Config;
use Vigilant\Exception\FeedsException;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

final class Feeds
{
    /**
     * @var array $feeds Feeds from feeds.yaml
     */
    private array $feeds = [];

    private array $feedDetails = [];

    /**
     * Constructor
     * 
     * @param string $path Feeds filepath
     */
    public function __construct(string $path)
    {
        $this->load($path);
        $this->validate();
    }

    /**
     * Get details of each feed in the YAML file.
     *
     * @return array
     */
    public function get(): array
    {
        return $this->feedDetails;
    }

    /**
     * Load feeds file
     *
     * @param string $path Feeds filepath
     * 
     * @throws FeedsException if file could not be read or the YAML is not valid.
     */
    private function load(string $path): void
    {
        try {
            Output::text('Loading feeds.yaml (' . $path . ')');

            $this->feeds = Yaml::parseFile($path);
        } catch (ParseException $err) {
            throw new FeedsException($err->getMessage());
        }
    }

    /**
     * Validate contents of feeds.yaml
     */
    private function validate(): void
    {
        Output::text('Validating feeds.yaml');

        if (array_key_exists('feeds', $this->feeds) === false || is_array($this->feeds['feeds']) === false) {
            throw new FeedsException('No feeds in feeds.yaml');
        }

        foreach ($this->feeds['feeds'] as $index => $feed) {
            if (array_key_exists('name', $feed) === false || $feed['name'] === null) {
                throw new FeedsException('No name given for feed ' . $index);
            }

            if (array_key_exists('url', $feed) === false || $feed['url'] === null) {
                throw new FeedsException("No url given for feed '" . $feed['name'] . "'");
            }

            if (array_key_exists('interval', $feed) === false || $feed['interval'] === '') {
                throw new FeedsException('No interval given for feed ' . "'" . $feed['name'] . "'");
            }

            if ($feed['interval'] < Config::getMinCheckInterval()) {
                throw new FeedsException('Interval is less than ' .
                    Config::getMinCheckInterval() . " seconds for feed '" . $feed['name'] . "'");
            }

            if (array_key_exists('gotify_token', $feed) === true && $feed['gotify_token'] === '') {
                throw new FeedsException('Empty Gotify token given for feed ' . "'" . $feed['name'] . "'");
            }

            $this->feedDetails[] = new FeedDetails($feed);
        }
    }
}
