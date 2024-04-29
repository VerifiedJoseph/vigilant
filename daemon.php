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
    Output::text(sprintf('Starting Vigilant %s daemon...', Version::get()));

    $config = new Config();
    $config->validate();

    $fetch = new Fetch();
    $logger = new Logger($config->getTimezone());
    $feeds = new Feeds($config, $logger);

    /** @phpstan-ignore-next-line */
    while (true) {
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

        sleep(30);
    }
} catch (ConfigException | AppException $err) {
    Output::text('[Vigilant] ' . $err->getMessage());
    exit(1);
}
