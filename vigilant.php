<?php

use Vigilant\Config;
use Vigilant\Feeds;
use Vigilant\Output;

use Vigilant\Exception\ConfigException;
use Vigilant\Exception\AppException;

require('vendor/autoload.php');

try {
    Config::load();

    $feeds = new Feeds(
        Config::getFeedsPath()
    );

    Output::newline();

    $feeds->check();

} catch (ConfigException | AppException $err) {
    Output::text($err->getMessage());
}
