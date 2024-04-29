<?php

namespace Vigilant\Config;

use DateTimeZone;
use Vigilant\Config\Validate;
use Vigilant\Exception\ConfigException;

class Validator extends AbstractValidator
{
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
     * Validate `VIGILANT_VERBOSE`
     * @throws ConfigException if value is not a boolean
     */
    public function verbose()
    {
        if ($this->hasEnv('VERBOSE') === true) {
            if ($this->isEnvBoolean('VERBOSE') === false) {
                throw new ConfigException('Verbose environment variable value must a boolean [VIGILANT_VERBOSE]');
            }

            if ($this->getEnv('VERBOSE') === 'true') {
                $this->config['logging_level'] = 2;
            }
        }
    }

    /**
     * Validate `VIGILANT_TIMEZONE`
     * @throws ConfigException if invalid timezone is given
     */
    public function timezone(): void
    {
        if ($this->hasEnv('TIMEZONE') === true && $this->getEnv('TIMEZONE') !== '') {
            if (in_array($this->getEnv('TIMEZONE'), DateTimeZone::listIdentifiers(DateTimeZone::ALL)) === false) {
                throw new ConfigException(sprintf(
                    'Invalid timezone: (%s). See: https://www.php.net/manual/en/timezones.php [VIGILANT_TIMEZONE]',
                    $this->getEnv('TIMEZONE')
                ));
            }

            $this->config['timezone'] = $this->getEnv('TIMEZONE');
        } else {
            $this->config['timezone'] = date_default_timezone_get();
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
            $gotify = new Validate\Gotify($this->config);
            $gotify->url();
            $gotify->priority();
            $gotify->token();
            $this->config = $gotify->getConfig();
        }

        if ($service === 'ntfy') {
            $ntfy = new Validate\Ntfy($this->config);
            $ntfy->url();
            $ntfy->topic();
            $ntfy->priority();
            $ntfy->auth();
            $this->config = $ntfy->getConfig();
        }
    }
}
