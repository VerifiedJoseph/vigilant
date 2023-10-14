<?php

namespace Vigilant\Config\Check;

use Vigilant\Exception\ConfigException;

final class Paths
{
    /**
     * Constructor
     *
     * @param string $cachePath Cache path
     */
    public function __construct(string $cachePath)
    {
        $this->feedCache($cachePath);
    }

    /**
     * Check feed cache path
     *
     * @param string $path Cache path
     */
    private function feedCache(string $path): void
    {
        if (is_dir($path) === false && mkdir($path) === false) {
            throw new ConfigException('Could not create cache directory:' . $path);
        }

        if (is_dir($path) === true && is_writable($path) === false) {
            throw new ConfigException('Cache directory is not writable: ' . $path);
        }
    }
}
