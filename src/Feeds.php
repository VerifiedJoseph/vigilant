<?php

namespace Vigilant;

use Exception;
use Vigilant\Config;

use Symfony\Component\Yaml\Yaml;

final class Feeds
{
	/**
	 * @var array $feeds Feeds details from feeds.yaml
	 */
	private array $feeds = [];

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->load();
		$this->validate();
		$this->calculateHashes();
	}

	public function get(): array
	{
		return $this->feeds['feeds'];
	}

	/**
	 * Load feeds file
	 */
	private function load(): void
	{
		Output::text('Loading feeds.yaml (' . Config::getFeedsPath() . ')');

		$this->feeds = Yaml::parseFile(Config::getFeedsPath());
	}

	/**
	 * 
	 */
	private function validate(): void
	{
		Output::text('Validating feeds.yaml');

		if (array_key_exists('feeds', $this->feeds) === false || is_array($this->feeds['feeds']) === false ) {
			throw new Exception('Error No feeds in feeds.yaml');
		}

		foreach ($this->get() as $index => $feed) {
			if (array_key_exists('name', $feed) === false || $feed['name'] === NULL) {
				throw new Exception('Feed error: No name given for feed ' . $index);
			}

			if (array_key_exists('url', $feed) === false || $feed['url'] === NULL) {
				throw new Exception("Feed error: No url given for feed '" . $feed['name'] . "'");
			}

			if (array_key_exists('interval', $feed) === false || $feed['interval'] === '') {
				throw new Exception("Feed error: No interval given for feed " . "'" . $feed['name'] . "'");
			}

			if ($feed['interval'] < Config::getMinCheckInterval()) {
				throw new Exception('Feed error: Interval is less than ' .  Config::getMinCheckInterval() . " seconds for feed '" . $feed['name'] . "'");
			}
		}
	}

	/**
	 * Calculate sha1 hash for each feed from its URL
	 */
	private function calculateHashes(): void
	{
		foreach ($this->get() as $index => $feed) {
			$this->feeds['feeds'][$index]['hash'] = sha1($feed['url']);
		}
	}
}