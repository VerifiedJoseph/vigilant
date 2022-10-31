<?php

namespace Vigilant\Config\Check;

use Vigilant\Config;
use Vigilant\Exception\ConfigException;

final class Paths
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->feedCache();
    }

    /**
     * Check feed cache path
     */
    private function feedCache(): void
    {
        if (is_dir(Config::getCachePath()) === false && mkdir(Config::getCachePath()) === false) {
            throw new ConfigException('Could not create cache directory:' . Config::getCachePath());
        }

        if (is_dir(Config::getCachePath()) === true && is_writable(Config::getCachePath()) === false) {
            throw new ConfigException('Cache directory is not writable: ' . Config::getCachePath());
        }
    }
}
