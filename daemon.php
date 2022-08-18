<?php

use Vigilant\Config;
use Vigilant\Feeds;
use Vigilant\Check;
use Vigilant\Output;

use Vigilant\Exception\ConfigException;

require('vendor/autoload.php');

try {
	Config::checkInstall();
	Config::checkConfig();

	$feeds = new Feeds();

	Output::text('---');

	foreach ($feeds->get() as $feed) {
		try {
			$check = new Check($feed);
			$check->run();

		} catch (Exception $err) {
			Output::text($err->getMessage());
		}
	}
} catch (ConfigException $err) {
	Output::text($err->getMessage());
}
