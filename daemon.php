<?php

use Vigilant\Config;
use Vigilant\Feeds;
use Vigilant\Check;
use Vigilant\Fetch;
use Vigilant\Notify;
use Vigilant\Output;
use Vigilant\Exception\ConfigException;
use Vigilant\Exception\AppException;

require('vendor/autoload.php');

try {
    Output::text('Starting Vigilant daemon...');

    $config = new Config();
    $config->validate();

    $fetch = new Fetch();
    $feeds = new Feeds($config);

    /** @phpstan-ignore-next-line */
    while (true) {
        foreach ($feeds->get() as $details) {
            $notify = new Notify($details, $config);
            $check = new Check(
                $details,
                $config,
                $fetch
            );

            if ($check->isDue() === true) {
                $check->check();
                $notify->send($check->getMessages());

                Output::text(sprintf(
                    'Next check in %s seconds at %s',
                    $details->getInterval(),
                    $check->getNextCheckDate()
                ));
            }
        }

        sleep(30);
    }
} catch (ConfigException | AppException $err) {
    Output::text($err->getMessage());
    exit(1);
}
