<?php

namespace Vigilant;

use Vigilant\Output;
use Vigilant\Config\Check\Install as CheckInstall;
use Vigilant\Config\Check\Paths as CheckPaths;
use Vigilant\Config\Check\Envs as checkEnvs;
use Vigilant\Exception\ConfigException;

final class Config
{
    /**
     * @var string $minPhpVersion Minimum PHP version
     */
    private string $minPhpVersion = '8.1.0';

    /**
     * @var array<int, string> $requiredPhpExtensions Required PHP extensions
     */
    private array $requiredPhpExtensions = ['xml', 'xmlreader', 'ctype'];

    /**
     * @var int $minCheckInterval Minimum feed check interval in seconds
     */
    private int $minCheckInterval = 300;

    /**
     * @var string $cachePath Cache folder path
     */
    private string $cachePath = 'cache';

    /**
     * @var array<int, string> $notificationServices Supported notification services
     */
    private array $notificationServices = ['gotify', 'ntfy'];

    /**
     * @var array<string, int|string|false> $defaults Default values for config parameters
     */
    private array $defaults = [
        'QUIET' => false,
        'FEEDS_FILE' => 'feeds.yaml',
        'NOTIFICATION_GOTIFY_PRIORITY' => 4,
        'NOTIFICATION_NTFY_PRIORITY' => 3,
        'NOTIFICATION_NTFY_AUTH' => false
    ];

    /**
     * @var array<string, mixed> $config Loaded config parameters
     */
    private array $config = [];

    /**
     * Check install and load config
     */
    public function load(): void
    {
        $this->checkInstall();
        $this->checkPaths();
        $this->checkEnvs();

        if ($this->config['QUIET'] === true) {
            Output::quiet();
        }
    }

    /**
     * Check PHP version and loaded extensions
     *
     * @throws ConfigException if PHP version is not supported
     * @throws ConfigException if a PHP extension is not loaded
     */
    public function checkInstall(): void
    {
        new CheckInstall();
    }

    /**
     * Check cache paths
     */
    public function checkPaths(): void
    {
        new CheckPaths();
    }

    /**
     * Check environment variables
     */
    public function checkEnvs(): void
    {
        $this->requireConfigFile();
        $this->setDefaults();

        $envs = new CheckEnvs($this->config);
        $this->config = $envs->getConfig();
    }

    /**
     * Returns config value
     *
     * @param string $key Config key
     * @return mixed
     * @throws ConfigException if config key is invalid
     */
    public function get(string $key): mixed
    {
        if (array_key_exists($key, $this->config) === false) {
            throw new ConfigException('Invalid config key given: ' . $key);
        }

        return $this->config[$key];
    }

    /**
     * Get minimum PHP version
     *
     * @return string
     */
    public function getMinPhpVersion(): string
    {
        return $this->minPhpVersion;
    }

    /**
     * Get notification services
     *
     * @return array<int, string>
     */
    public function getNotificationServices(): array
    {
        return $this->notificationServices;
    }

    /**
     * Get required PHP extensions
     *
     * @return array<int, string>
     */
    public function getRequiredPhpExtensions(): array
    {
        return $this->requiredPhpExtensions;
    }

    /**
     * Get cache path
     *
     * @return string
     */
    public function getCachePath(): string
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . $this->cachePath;
    }

    /**
     * Get minimum feed check interval
     *
     * @return int
     */
    public function getMinCheckInterval(): int
    {
        return $this->minCheckInterval;
    }

    /**
     * Get feeds path
     *
     * @return string
     */
    public function getFeedsPath(): string
    {
        return $this->config['FEEDS_FILE'];
    }

    /**
     * Include (require) config file
     */
    private function requireConfigFile(): void
    {
        if (file_exists('config.php') === true) {
            require('config.php');
        }
    }

    /**
     * Set defaults as config values
     */
    private function setDefaults(): void
    {
        $this->config = $this->defaults;
        $this->config['FEEDS_FILE'] = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'feeds.yaml';
    }
}
