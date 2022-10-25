<?php

namespace Vigilant;

use Vigilant\Exception\ConfigException;

final class Config
{
    /**
     * @var string $minPhpVersion Minimum PHP version
     */
    private static string $minPhpVersion = '8.1.0';

    /**
     * @var int $minCheckInterval Minimum feed check interval in seconds
     */
    private static int $minCheckInterval = 300;

    /**
     * @var string $cachePath Cache folder path
     */
    private static string $cachePath = 'cache';

    /**
     * @var string $feedsPath feeds file path
     */
    private static string $feedsPath = 'feeds.yaml';

    /**
     * @var array $notificationServices Supported notification services
     */
    private static array $notificationServices = ['gotify', 'ntfy'];

    /**
     * @var array $defaults Default values for config parameters
     */
    private static array $defaults = [
        'NOTIFICATION_GOTIFY_PRIORITY' => 4,
        'NOTIFICATION_NTFY_PRIORITY' => 3,
    ];

    /**
     * @var array $config Loaded config parameters
     */
    private static array $config = [];

    /**
     * Check PHP version and loaded extensions
     *
     * @throws ConfigException if PHP version is not supported
     * @throws ConfigException if a PHP extension is not loaded
     */
    public static function checkInstall()
    {
        if (version_compare(PHP_VERSION, self::$minPhpVersion) === -1) {
            throw new ConfigException('Vigilant requires at least PHP version ' . self::$minPhpVersion . '!');
        }

        if (extension_loaded('curl') === false) {
            throw new ConfigException('Extension Error: cURL extension not loaded.');
        }

        if (extension_loaded('json') === false) {
            throw new ConfigException('Extension Error: JSON extension not loaded.');
        }
    }

    /**
     * Check config
     *
     * @throws ConfigException if cache directory could not be created.
     * @throws ConfigException if cache directory is not writable.
     * @throws ConfigException if SimplePie cache directory could not be created.
     * @throws ConfigException if SimplePie cache directory is not writable.
     */
    public static function checkConfig(): void
    {
        self::requireConfigFile();
        self::setDefaults();

        if (is_dir(self::getCachePath()) === false && mkdir(self::getCachePath()) === false) {
            throw new ConfigException('Could not create cache directory:' . self::getCachePath());
        }

        if (is_dir(self::getCachePath()) === true && is_writable(self::getCachePath()) === false) {
            throw new ConfigException('Cache directory is not writable: ' . self::getCachePath());
        }

        if (is_dir(self::getSimplePieCachePath()) === false && mkdir(self::getSimplePieCachePath()) === false) {
            throw new ConfigException('Could not create SimplePie cache directory:' . self::getSimplePieCachePath());
        }

        if (is_dir(self::getSimplePieCachePath()) === true && is_writable(self::getSimplePieCachePath()) === false) {
            throw new ConfigException('SimplePie cache directory is not writable: ' . self::getSimplePieCachePath());
        }

        if (file_exists(self::getFeedsPath()) == false) {
            throw new ConfigException('Feeds file not found: ' . self::getFeedsPath());
        }

        self::checkEnvs();
    }

    /**
     * Returns config value
     *
     * @param string $key Config key
     * @return string|int|boolean
     * @throws ConfigException if config key is invalid
     */
    public static function get(string $key): string|int|bool
    {
        if (array_key_exists($key, self::$config) === false) {
            throw new ConfigException('Invalid config key given: ' . $key);
        }

        return self::$config[$key];
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
     * Get SimplePie cache path
     *
     * @return string
     */
    public static function getSimplePieCachePath(): string
    {
        return self::getCachePath() . DIRECTORY_SEPARATOR . 'simplepie';
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
    public static function getFeedsPath()
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . self::$feedsPath;
    }

    /**
     * Include (require) config file
     */
    private static function requireConfigFile()
    {
        if (file_exists('config.php') === true) {
            require('config.php');
        }
    }

    /**
     * Set defaults as config values
     */
    private static function setDefaults()
    {
        self::$config = self::$defaults;
    }

    /**
     * Check if a environment variable is set
     *
     * @return bool
     */
    private static function isEnvSet(string $name): bool
    {
        if (getenv('VIGILANT_' . $name) !== false) {
            return true;
        }

        return false;
    }

    /**
     * Get environment variable value
     *
     * @param string $name Environment variable name
     * @return mixed
     */
    private static function getEnv(string $name): mixed
    {
        return getenv('VIGILANT_' . $name);
    }

    /**
     * Check environment variables
     *
     * @throws ConfigException if a environment variable is not given.
     * @throws ConfigException if a environment variable is invalid.
     */
    private static function checkEnvs(): void
    {
        if (self::isEnvSet('NOTIFICATION_SERVICE') === false) {
            throw new ConfigException('No notification service given [VIGILANT_NOTIFICATION_SERVICE]');
        }

        $noteService = strtolower(self::getEnv('NOTIFICATION_SERVICE'));

        if (in_array($noteService, self::$notificationServices) === false) {
            throw new ConfigException('Unknown notification service given. [VIGILANT_NOTIFICATION_SERVICE]');
        }

        self::$config['NOTIFICATION_SERVICE'] = $noteService;

        if ($noteService === 'gotify') {
            if (self::isEnvSet('NOTIFICATION_GOTIFY_URL') === false) {
                throw new ConfigException('No Gotify URL given [VIGILANT_NOTIFICATION_GOTIFY_URL]');
            }

            self::$config['NOTIFICATION_GOTIFY_URL'] = self::getEnv('NOTIFICATION_GOTIFY_URL');

            if (self::isEnvSet('NOTIFICATION_GOTIFY_TOKEN') === false) {
                throw new ConfigException('No Gotify app token given [VIGILANT_NOTIFICATION_GOTIFY_TOKEN]');
            }

            self::$config['NOTIFICATION_GOTIFY_TOKEN'] = self::getEnv('NOTIFICATION_GOTIFY_TOKEN');
        }

        if ($noteService === 'ntfy') {
            if (self::isEnvSet('NOTIFICATION_NTFY_URL') === false) {
                throw new ConfigException('No ntfy URL given [VIGILANT_NOTIFICATION_NTFY_URL]');
            }

            self::$config['NOTIFICATION_NTFY_URL'] = self::getEnv('NOTIFICATION_NTFY_URL');

            if (self::isEnvSet('NOTIFICATION_NTFY_TOPIC') === false) {
                throw new ConfigException('No ntfy topic given [VIGILANT_NOTIFICATION_NTFY_TOPIC]');
            }

            self::$config['NOTIFICATION_NTFY_TOPIC'] = self::getEnv('NOTIFICATION_NTFY_TOPIC');
        }
    }
}
