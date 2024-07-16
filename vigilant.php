<?php

use Vigilant\Config;
use Vigilant\Feeds;
use Vigilant\Check;
use Vigilant\Fetch;
use Vigilant\Notify;
use Vigilant\Output;
use Vigilant\Logger;
use Vigilant\Version;
use Vigilant\ActiveHours;
use Vigilant\Exception\ConfigException;
use Vigilant\Exception\AppException;

require('vendor/autoload.php');

try {
    $config = new Config();
    $config->validate();

    $logger = new Logger(
        $config->getTimezone(),
        $config->getLoggingLevel()
    );
    $logger->debug(sprintf('Vigilant v%s', Version::get()));

    $fetch = new Fetch();
    $feeds = new Feeds($config, $logger);

    foreach ($feeds->get() as $details) {
        $notify = new Notify($details, $config, $logger);
        $check = new Check(
            $details,
            $fetch,
            $config,
            $logger
        );

        $now = new DateTime('now', new DateTimeZone($config->getTimezone()));
        $activeHours = new ActiveHours(
            $now,
            $details->getActiveHoursStartTime(),
            $details->getActiveHoursEndTime(),
            $config->getTimezone(),
            $logger
        );

        if ($details->hasActiveHours() === false || $activeHours->isEnabled() === true) {
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
    }
} catch (ConfigException | AppException $err) {
    Output::text($err->getMessage());
    exit(1);
}
