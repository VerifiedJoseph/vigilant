<?php

use Vigilant\Config;
use Vigilant\Feeds;
use Vigilant\Check;
use Vigilant\Fetch;
use Vigilant\Notify;
use Vigilant\Output;
use Vigilant\Logger;
use Vigilant\Version;
use Vigilant\Exception\ConfigException;
use Vigilant\Exception\AppException;

require('vendor/autoload.php');

try {
    $config = new Config();
    $config->validate();

    $fetch = new Fetch();
    $logger = new Logger($config->getTimezone());
    $feeds = new Feeds($config, $logger);

    foreach ($feeds->get() as $details) {
        $notify = new Notify($details, $config, $logger);
        $check = new Check(
            $details,
            $fetch,
            $config,
            $logger
        );

        if ($check->isDue() === true) {
            $check->check();
            $notify->send($check->getMessages());

            $logger->info(sprintf(
                'Next check in %s seconds at %s',
                $details->getInterval(),
                $check->getNextCheckDate()
            ));
        }
    }
} catch (ConfigException | AppException $err) {
    Output::text($err->getMessage());
}
