<?php

namespace Vigilant;

use Vigilant\Config;
use Vigilant\Exception\FeedsException;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

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
	 * 
	 * @throws FeedsException if file could not be read or the YAML is not valid.
	 */
	private function load(): void
	{
		try {
			Output::text('Loading feeds.yaml (' . Config::getFeedsPath() . ')');

			$this->feeds = Yaml::parseFile(Config::getFeedsPath());

		} catch (ParseException $err) {
			throw new FeedsException($err->getMessage());
		}
	}

	/**
	 *
	 */
	private function validate(): void
	{
		Output::text('Validating feeds.yaml');

		if (array_key_exists('feeds', $this->feeds) === false || is_array($this->feeds['feeds']) === false) {
			throw new FeedsException('No feeds in feeds.yaml');
		}

		foreach ($this->get() as $index => $feed) {
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
