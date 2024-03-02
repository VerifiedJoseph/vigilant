<?php

use Vigilant\Config;
use Vigilant\Feeds;
use Vigilant\Output;
use Vigilant\Exception\ConfigException;
use Vigilant\Exception\AppException;

require('vendor/autoload.php');

try {
    $config = new Config();
    $config->load();

    $feeds = new Feeds($config);

    Output::text('Running...');

    while (true) {
        $feeds->check();

        sleep(30);
    }
} catch (ConfigException | AppException $err) {
    Output::text($err->getMessage());
}
