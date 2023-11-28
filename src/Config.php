<?php

namespace Vigilant;

use Vigilant\Output;
use Vigilant\Config\Check\Install as CheckInstall;
use Vigilant\Config\Check\Paths as CheckPaths;
use Vigilant\Config\Check\Envs as checkEnvs;
use Vigilant\Exception\ConfigException;

class Config
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
        new CheckInstall(
            $this->getMinPhpVersion(),
            $this->getRequiredPhpExtensions()
        );
    }

    /**
     * Check cache paths
     */
    public function checkPaths(): void
    {
        new CheckPaths($this->getCachePath());
    }

    /**
     * Check environment variables
     */
    public function checkEnvs(): void
    {
        $this->requireConfigFile();
        $this->setDefaults();

        $envs = new CheckEnvs(
            $this->config,
            $this->notificationServices
        );

        $this->config = $envs->getConfig();
    }

    /**
     * Returns enabled notification service
     *
     * @return string
     */
    public function getNotificationService(): string
    {
        return $this->config['NOTIFICATION_SERVICE'];
    }

    /**
     * Returns URL for ntfy.sh notification service
     *
     * @return string
     */
    public function getNtfyUrl(): string
    {
        return $this->config['NOTIFICATION_NTFY_URL'];
    }

    /**
     * Returns topic for ntfy.sh notification service
     *
     * @return string
     */
    public function getNtfyTopic(): string
    {
        return $this->config['NOTIFICATION_NTFY_TOPIC'];
    }

    /**
     * Returns priority for ntfy.sh notification service
     *
     * @return int
     */
    public function getNtfyPriority(): int
    {
        return $this->config['NOTIFICATION_NTFY_PRIORITY'];
    }

    /**
     * Returns auth status for ntfy.sh notification service
     *
     * @return bool
     */
    public function getNtfyAuth(): bool
    {
        return $this->config['NOTIFICATION_NTFY_AUTH'];
    }

    /**
     * Returns username for ntfy.sh notification service
     *
     * @return string
     */
    public function getNtfyUsername(): string
    {
        return $this->config['NOTIFICATION_NTFY_USERNAME'];
    }

    /**
     * Returns password for ntfy.sh notification service
     *
     * @return string
     */
    public function getNtfyPassword(): string
    {
        return $this->config['NOTIFICATION_NTFY_PASSWORD'];
    }

    /**
     * Returns URL for Gotify notification service
     *
     * @return string
     */
    public function getGotifyUrl(): string
    {
        return $this->config['NOTIFICATION_GOTIFY_URL'];
    }

    /**
     * Returns priority for Gotify notification service
     *
     * @return int
     */
    public function getGotifyPriority(): int
    {
        return $this->config['NOTIFICATION_GOTIFY_PRIORITY'];
    }

    /**
     * Returns token for Gotify notification service
     *
     * @return string
     */
    public function getGotifyToken(): string
    {
        return $this->config['NOTIFICATION_GOTIFY_TOKEN'];
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
