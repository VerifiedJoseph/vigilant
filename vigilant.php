<?php

declare(strict_types=1);

use Vigilant\Config;
use Vigilant\Feeds;
use Vigilant\Notify;
use Vigilant\Logger;
use Vigilant\Version;
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

    $feeds = new Feeds($config, $logger);

    foreach ($feeds->get() as $feed) {
        $notify = new Notify($feed->details, $config, $logger);

        if ($feed->details->hasActiveHours() === false || $feed->activeHours->isEnabled() === true) {
            if ($feed->check->isDue() === true) {
                $feed->check->check();
                $notify->send($feed->check->getMessages());

                if (
                    $feed->details->hasActiveHours() === true &&
                    $feed->check->getNextCheckDate('U') >= $feed->activeHours->getEndTime('U')
                ) {
                    $logger->info(sprintf(
                        'Next check during active hours starting at %s',
                        $feed->details->getActiveHoursStartTime()
                    ));
                } else {
                    $logger->info(sprintf(
                        'Next check in %s seconds at %s',
                        $feed->details->getInterval(),
                        $feed->check->getNextCheckDate()
                    ));
                }
            }
        }
    }
} catch (ConfigException | AppException $err) {
    (new Logger(date_default_timezone_get()))->info($err->getMessage());
    exit(1);
}
