<?php

namespace Vigilant;

use Vigilant\Config\Validate;

class Config
{
    private Validate $validate;

    /** @var string $minPhpVersion Minimum PHP version */
    private string $minPhpVersion = '8.1.0';

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
        'QUIET' => false,
        'feeds_file' => 'feeds.yaml',
        'notification_gotify_priority' => 4,
        'NOTIFICATION_NTFY_PRIORITY' => 3,
        'NOTIFICATION_NTFY_AUTH' => 'none'
    ];

    /** @var array<string, mixed> $config Loaded config parameters */
    private array $config = [];

    public function __construct()
    {
        $this->validate = new Validate($this->defaults);
        $this->validate->version(PHP_VERSION, $this->minPhpVersion);
        $this->validate->extensions($this->extensions);

        $this->config = $this->defaults;
        $this->config['feeds_file'] = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'feeds.yaml';
    }

    /**
     * Validate environment variables
     */
    public function validate(): void
    {
        $this->requireConfigFile();

        $this->validate->folder($this->getCachePath());
        $this->validate->feedsFile();
        $this->validate->notificationService($this->notificationServices);
        $this->config = $this->validate->getConfig();
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
     * Returns auth method for ntfy.sh notification service
     *
     * @return string
     */
    public function getNtfyAuthMethod(): string
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
     * Returns token for ntfy.sh notification service
     *
     * @return string
     */
    public function getNtfyToken(): string
    {
        return $this->config['NOTIFICATION_NTFY_TOKEN'];
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
     * Include (require) config file
     */
    private function requireConfigFile(): void
    {
        if (file_exists('config.php') === true) {
            require('config.php');
        }
    }
}
