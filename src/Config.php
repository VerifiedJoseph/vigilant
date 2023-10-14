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
    private static string $minPhpVersion = '8.1.0';

    /**
     * @var array<int, string> $requiredPhpExtensions Required PHP extensions
     */
    private static array $requiredPhpExtensions = ['xml', 'xmlreader', 'ctype'];

    /**
     * @var int $minCheckInterval Minimum feed check interval in seconds
     */
    private static int $minCheckInterval = 300;

    /**
     * @var string $cachePath Cache folder path
     */
    private static string $cachePath = 'cache';

    /**
     * @var array<int, string> $notificationServices Supported notification services
     */
    private static array $notificationServices = ['gotify', 'ntfy'];

    /**
     * @var array<string, int|string|false> $defaults Default values for config parameters
     */
    private static array $defaults = [
        'QUIET' => false,
        'FEEDS_FILE' => 'feeds.yaml',
        'NOTIFICATION_GOTIFY_PRIORITY' => 4,
        'NOTIFICATION_NTFY_PRIORITY' => 3,
        'NOTIFICATION_NTFY_AUTH' => false
    ];

    /**
     * @var array<string, mixed> $config Loaded config parameters
     */
    private static array $config = [];

    /**
     * Check install and load config
     */
    public static function load(): void
    {
        self::checkInstall();
        self::checkPaths();
        self::checkEnvs();

        if (self::$config['QUIET'] === true) {
            Output::quiet();
        }
    }

    /**
     * Check PHP version and loaded extensions
     *
     * @throws ConfigException if PHP version is not supported
     * @throws ConfigException if a PHP extension is not loaded
     */
    public static function checkInstall(): void
    {
        new CheckInstall();
    }

    /**
     * Check cache paths
     */
    public static function checkPaths(): void
    {
        new CheckPaths();
    }

    /**
     * Check environment variables
     */
    public static function checkEnvs(): void
    {
        self::requireConfigFile();
        self::setDefaults();

        $envs = new CheckEnvs(self::$config);
        self::$config = $envs->getConfig();
    }

    /**
     * Returns config value
     *
     * @param string $key Config key
     * @return mixed
     * @throws ConfigException if config key is invalid
     */
    public static function get(string $key): mixed
    {
        if (array_key_exists($key, self::$config) === false) {
            throw new ConfigException('Invalid config key given: ' . $key);
        }

        return self::$config[$key];
    }

    /**
     * Get minimum PHP version
     *
     * @return string
     */
    public static function getMinPhpVersion(): string
    {
        return self::$minPhpVersion;
    }

    /**
     * Get notification services
     *
     * @return array<int, string>
     */
    public static function getNotificationServices(): array
    {
        return self::$notificationServices;
    }

    /**
     * Get required PHP extensions
     *
     * @return array<int, string>
     */
    public static function getRequiredPhpExtensions(): array
    {
        return self::$requiredPhpExtensions;
    }

    /**
     * Get cache path
     *
     * @return string
     */
    public static function getCachePath(): string
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . self::$cachePath;
    }

    /**
     * Get minimum feed check interval
     *
     * @return int
     */
    public static function getMinCheckInterval(): int
    {
        return self::$minCheckInterval;
    }

    /**
     * Get feeds path
     *
     * @return string
     */
    public static function getFeedsPath(): string
    {
        return self::$config['FEEDS_FILE'];
    }

    /**
     * Include (require) config file
     */
    private static function requireConfigFile(): void
    {
        if (file_exists('config.php') === true) {
            require('config.php');
        }
    }

    /**
     * Set defaults as config values
     */
    private static function setDefaults(): void
    {
        self::$config = self::$defaults;
        self::$config['FEEDS_FILE'] = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'feeds.yaml';
    }
}
