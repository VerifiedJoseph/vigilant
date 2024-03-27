<?php

namespace Vigilant\Config;

use Vigilant\Exception\ConfigException;

class Validate extends Base
{
    /** @var array<string, mixed> $config Config */
    private array $config = [];

    /**
     * @param array<string, mixed> $defaults Config defaults
     */
    public function __construct(array $defaults)
    {
        $this->config = $defaults;
    }

    /**
     * Returns config
     *
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Check php version
     *
     * @param string $version php version
     * @param string $minimumVersion Minimum required PHP version
     * @throws ConfigException if PHP version not supported.
     */
    public function version(string $version, string $minimumVersion): void
    {
        if (version_compare($version, $minimumVersion) === -1) {
            throw new ConfigException('Vigilant requires at least PHP version ' . $minimumVersion);
        }
    }

    /**
     * Check for required php extensions
     *
     * @param array<int, string> $required Required php extensions
     * @throws ConfigException if a required PHP extension is not loaded.
     */
    public function extensions(array $required): void
    {
        foreach ($required as $ext) {
            if (extension_loaded($ext) === false) {
                throw new ConfigException(sprintf('PHP extension error: %s extension not loaded.', $ext));
            }
        }
    }


    /**
     * Check for folder and create when needed
     *
     * @param string $path Folder path
     * @throws ConfigException if data folder could not be created.
     */
    public function folder(string $path): void
    {
        if (file_exists($path) === false) {
            if (mkdir($path) === false) {
                throw new ConfigException('Failed to create folder: ' . $path);
            }
        }
    }

    /**
     * Check `VIGILANT_FEEDS_FILE`
     *
     * @throws ConfigException if feeds file is not found
     * @throws ConfigException if feeds file is not readable
     */
    public function feedsFile(): void
    {
        if ($this->hasEnv('FEEDS_FILE') === true) {
            $file = $this->getEnv('FEEDS_FILE');

            if (file_exists($file) === false || is_readable($file) === false) {
                throw new ConfigException(
                    sprintf('Feeds file does not exist or not readable: %s [VIGILANT_FEEDS_FILE]', $file)
                );
            }

            $this->config['feeds_file'] = $this->getEnv('FEEDS_FILE');
        }
    }

    /**
     * Check `VIGILANT_NOTIFICATION_SERVICE`
     *
     * @param array<int, string> $supportedServices Supported notification services
     *
     * @throws ConfigException if no notification service is given
     * @throws ConfigException if unknown notification service is given
     */
    public function notificationService(array $supportedServices): void
    {
        if ($this->hasEnv('NOTIFICATION_SERVICE') === false) {
            throw new ConfigException('No notification service given [VIGILANT_NOTIFICATION_SERVICE]');
        }

        $service = strtolower($this->getEnv('NOTIFICATION_SERVICE'));

        if (in_array($service, $supportedServices) === false) {
            throw new ConfigException('Unknown notification service given. [VIGILANT_NOTIFICATION_SERVICE]');
        }

        $this->config['notification_service'] = $service;

        if ($service === 'gotify') {
            $this->notificationGotify();
        }

        if ($service === 'ntfy') {
            $this->notificationNtfy();
        }
    }

    /**
     * Check `VIGILANT_NOTIFICATION_GOTIFY_*`
     *
     * @throws ConfigException if no Gotify URL is given
     * @throws ConfigException if no Gotify app token is given
     */
    public function notificationGotify(): void
    {
        if ($this->hasEnv('NOTIFICATION_GOTIFY_URL') === false) {
            throw new ConfigException('No Gotify URL given [VIGILANT_NOTIFICATION_GOTIFY_URL]');
        }

        $this->config['notification_gotify_url'] = $this->getEnv('NOTIFICATION_GOTIFY_URL');

        if ($this->hasEnv('NOTIFICATION_GOTIFY_TOKEN') === false) {
            throw new ConfigException('No Gotify app token given [VIGILANT_NOTIFICATION_GOTIFY_TOKEN]');
        }

        $this->config['notification_gotify_token'] = $this->getEnv('NOTIFICATION_GOTIFY_TOKEN');
    }

    /**
     * Check `VIGILANT_NOTIFICATION_NTFY_*`
     *
     * @throws ConfigException if no Ntfy URL is given
     * @throws ConfigException if no Ntfy topic is given
     * @throws ConfigException if an unknown Ntfy auth type is given
     * @throws ConfigException if no Ntfy auth username is given and is required
     * @throws ConfigException if no Ntfy auth password is given and is required
     */
    public function notificationNtfy(): void
    {
        if ($this->hasEnv('NOTIFICATION_NTFY_URL') === false) {
            throw new ConfigException('No ntfy URL given [VIGILANT_NOTIFICATION_NTFY_URL]');
        }

        $this->config['NOTIFICATION_NTFY_URL'] = $this->getEnv('NOTIFICATION_NTFY_URL');

        if ($this->hasEnv('NOTIFICATION_NTFY_TOPIC') === false) {
            throw new ConfigException('No ntfy topic given [VIGILANT_NOTIFICATION_NTFY_TOPIC]');
        }

        $this->config['NOTIFICATION_NTFY_TOPIC'] = $this->getEnv('NOTIFICATION_NTFY_TOPIC');

        if ($this->hasEnv('NOTIFICATION_NTFY_AUTH') === true) {
            if (in_array($this->getEnv('NOTIFICATION_NTFY_AUTH'), ['password', 'token']) === false) {
                throw new ConfigException(
                    'Unknown ntfy authentication method given [VIGILANT_NOTIFICATION_NTFY_USERNAME]'
                );
            }

            $this->config['NOTIFICATION_NTFY_AUTH'] = $this->getEnv('NOTIFICATION_NTFY_AUTH');

            if ($this->getEnv('NOTIFICATION_NTFY_AUTH') === 'password') {
                if ($this->hasEnv('NOTIFICATION_NTFY_USERNAME') === false) {
                    throw new ConfigException(
                        'No ntfy authentication username given [VIGILANT_NOTIFICATION_NTFY_USERNAME]'
                    );
                }

                $this->config['NOTIFICATION_NTFY_USERNAME'] = $this->getEnv('NOTIFICATION_NTFY_USERNAME');

                if ($this->hasEnv('NOTIFICATION_NTFY_PASSWORD') === false) {
                    throw new ConfigException(
                        'No ntfy authentication password given [VIGILANT_NOTIFICATION_NTFY_PASSWORD]'
                    );
                }

                $this->config['NOTIFICATION_NTFY_PASSWORD'] = $this->getEnv('NOTIFICATION_NTFY_PASSWORD');
            }

            if ($this->getEnv('NOTIFICATION_NTFY_AUTH') === 'token') {
                if ($this->hasEnv('NOTIFICATION_NTFY_TOKEN') === false) {
                    throw new ConfigException(
                        'No ntfy authentication token given [VIGILANT_NOTIFICATION_NTFY_TOKEN]'
                    );
                }

                $this->config['NOTIFICATION_NTFY_TOKEN'] = $this->getEnv('NOTIFICATION_NTFY_TOKEN');
            }
        }
    }
}
