<?php

use Vigilant\Config;
use Vigilant\Feeds;
use Vigilant\Check;
use Vigilant\Fetch;
use Vigilant\Output;
use Vigilant\Exception\ConfigException;
use Vigilant\Exception\AppException;

require('vendor/autoload.php');

try {
    $config = new Config();
    $config->validate();

    $feeds = new Feeds($config);
    Output::newline();

    foreach($feeds->get() as $feed) {
        $check = new Check($feed, $config, new Fetch());
        $check->run();
    }
} catch (ConfigException | AppException $err) {
    Output::text($err->getMessage());
}
