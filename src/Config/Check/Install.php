<?php

namespace Vigilant\Config\Check;

use Vigilant\Exception\ConfigException;

final class Install
{
    /**
     * Constructor
     * 
     * @param string $minPhpVersion Minimum supported PHP version
     * @param array<int, string> $requiredPhpExtensions Required PHP extensions
     */
    public function __construct(string $minPhpVersion, array $requiredPhpExtensions)
    {
        $this->version($minPhpVersion);
        $this->extensions($requiredPhpExtensions);
    }

    /**
     * Check PHP version
     *
     * @param string $minPhpVersion Minimum supported PHP version
     * @throws ConfigException if PHP version is not supported
     */
    private function version($minPhpVersion): void
    {
        if (version_compare(PHP_VERSION, $minPhpVersion) === -1) {
            throw new ConfigException('Vigilant requires at least PHP version ' . $minPhpVersion . '!');
        }
    }

    /**
     * Check PHP extensions
     *
     * @param array<int, string> $requiredPhpExtensions Required PHP extensions
     * @throws ConfigException if a PHP extension is not loaded
     */
    private function extensions($requiredPhpExtensions): void
    {
        foreach ($requiredPhpExtensions as $ext) {
            if (extension_loaded($ext) === false) {
                throw new ConfigException('Extension Error: ' . $ext . ' extension not loaded.');
            }
        }
    }
}
