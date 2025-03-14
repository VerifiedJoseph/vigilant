<?php

declare(strict_types=1);

namespace Vigilant;

use Vigilant\Config\Validator;

class Config
{
    private Validator $validate;

    /** @var string $minPhpVersion Minimum PHP version */
    private string $minPhpVersion = '8.2.0';

    /** @var array<int, string> $extensions Required PHP extensions */
    private array $extensions = ['xml', 'xmlreader', 'ctype'];

    /** @var int $minCheckInterval Minimum feed check interval in seconds */
    private int $minCheckInterval = 300;

    /** @var string $cachePath Cache folder path */
    private string $cachePath = 'cache';

    /** @var array<int, string> $notificationServices Supported notification services */
    private array $notificationServices = ['gotify', 'ntfy'];

    /** @var array<string, int|string|false> $defaults Default values for config parameters */
    private array $defaults = [
        'logging_level' => 1,
        'feeds_file' => 'feeds.yaml',
        'notification_gotify_priority' => 4,
        'notification_ntfy_priority' => 3,
        'notification_ntfy_auth' => 'none'
    ];

    /** @var array<string, mixed> $config Loaded config parameters */
    private array $config = [];

    public function __construct()
    {
        $this->validate = new Validator($this->defaults);
        $this->validate->version(PHP_VERSION, $this->minPhpVersion);
        $this->validate->extensions($this->extensions);

        $this->config = $this->defaults;
        $this->config['timezone'] = date_default_timezone_get();
        $this->config['feeds_file'] = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'feeds.yaml';
    }

    /**
     * Validate environment variables
     */
    public function validate(): void
    {
        $this->requireConfigFile();

        $this->validate->verbose();
        $this->validate->timezone();
        $this->validate->folder($this->getCachePath());
        $this->validate->feedsFile();
        $this->validate->notificationService($this->notificationServices);
        $this->config = $this->validate->getConfig();
    }

    /**
     * Return logging level
     * @return int
     */
    public function getLoggingLevel(): int
    {
        return $this->config['logging_level'];
    }

    /**
     * Returns timezone
     *
     * @return string
     */
    public function getTimezone(): string
    {
        return $this->config['timezone'];
    }

    /**
     * Returns enabled notification service
     *
     * @return string
     */
    public function getNotificationService(): string
    {
        return $this->config['notification_service'];
    }

    /**
     * Returns URL for ntfy.sh notification service
     *
     * @return string
     */
    public function getNtfyUrl(): string
    {
        return $this->config['notification_ntfy_url'];
    }

    /**
     * Returns topic for ntfy.sh notification service
     *
     * @return string
     */
    public function getNtfyTopic(): string
    {
        return $this->config['notification_ntfy_topic'];
    }

    /**
     * Returns priority for ntfy.sh notification service
     *
     * @return int
     */
    public function getNtfyPriority(): int
    {
        return $this->config['notification_ntfy_priority'];
    }

    /**
     * Returns auth method for ntfy.sh notification service
     *
     * @return string
     */
    public function getNtfyAuthMethod(): string
    {
        return $this->config['notification_ntfy_auth'];
    }

    /**
     * Returns username for ntfy.sh notification service
     *
     * @return string
     */
    public function getNtfyUsername(): string
    {
        return $this->config['notification_ntfy_username'];
    }

    /**
     * Returns password for ntfy.sh notification service
     *
     * @return string
     */
    public function getNtfyPassword(): string
    {
        return $this->config['notification_ntfy_password'];
    }

    /**
     * Returns token for ntfy.sh notification service
     *
     * @return string
     */
    public function getNtfyToken(): string
    {
        return $this->config['notification_ntfy_token'];
    }

    /**
     * Returns URL for Gotify notification service
     *
     * @return string
     */
    public function getGotifyUrl(): string
    {
        return $this->config['notification_gotify_url'];
    }

    /**
     * Returns priority for Gotify notification service
     *
     * @return int
     */
    public function getGotifyPriority(): int
    {
        return $this->config['notification_gotify_priority'];
    }

    /**
     * Returns token for Gotify notification service
     *
     * @return string
     */
    public function getGotifyToken(): string
    {
        return $this->config['notification_gotify_token'];
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
        return $this->config['feeds_file'];
    }

    /**
     * Returns cache format version
     * @return int
     */
    public function getCacheFormatVersion(): int
    {
        return Version::getCacheFormatVersion();
    }

    /**
     * Include (require) config file
     *
     * @codeCoverageIgnore
     */
    private function requireConfigFile(): void
    {
        if (file_exists('config.php') === true) {
            include_once('config.php');
        }
    }
}
