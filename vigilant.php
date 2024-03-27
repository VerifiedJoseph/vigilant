<?php

use Vigilant\Config;
use Vigilant\Feeds;
use Vigilant\Output;
use Vigilant\Exception\ConfigException;
use Vigilant\Exception\AppException;

require('vendor/autoload.php');

try {
    $config = new Config();
    $config->validate();

    $feeds = new Feeds($config);

    Output::newline();

    $feeds->check();
} catch (ConfigException | AppException $err) {
    Output::text($err->getMessage());
}
