<?php

namespace Vigilant\Config\Check;

use Vigilant\Config;
use Vigilant\Exception\ConfigException;

final class Install
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->version();
        $this->extensions();
    }

    /**
     * Check PHP version
     *
     * @throws ConfigException if PHP version is not supported
     */
    private function version(): void
    {
        if (version_compare(PHP_VERSION, Config::getMinPhpVersion()) === -1) {
            throw new ConfigException('Vigilant requires at least PHP version ' . Config::getMinPhpVersion() . '!');
        }
    }

    /**
     * Check PHP extensions
     *
     * @throws ConfigException if a PHP extension is not loaded
     */
    private function extensions(): void
    {
        foreach (Config::getRequiredPhpExtensions() as $ext) {
            if (extension_loaded($ext) === false) {
                throw new ConfigException('Extension Error: ' . $ext . ' extension not loaded.');
            }
        }
    }
}
