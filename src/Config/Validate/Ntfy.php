<?php

declare(strict_types=1);

namespace Vigilant\Config\Validate;

use Vigilant\Config\AbstractValidator;
use Vigilant\Exception\ConfigException;

class Ntfy extends AbstractValidator
{
    /**
     * Validate `VIGILANT_NOTIFICATION_NTFY_URL`
     *
     * @throws ConfigException if no ntfy URL is given
     */
    public function url(): void
    {
        if ($this->hasEnv('NOTIFICATION_NTFY_URL') === false) {
            throw new ConfigException('No ntfy URL given [VIGILANT_NOTIFICATION_NTFY_URL]');
        }

        $this->config['notification_ntfy_url'] = $this->getEnv('NOTIFICATION_NTFY_URL');
    }

    /**
     * Validate `VIGILANT_NOTIFICATION_NTFY_PRIORITY`
     *
     * @throws ConfigException if priority is not a number
     */
    public function priority(): void
    {
        if ($this->hasEnv('NOTIFICATION_NTFY_PRIORITY') === true) {
            if (is_numeric($this->getEnv('NOTIFICATION_NTFY_PRIORITY')) === false) {
                throw new ConfigException('Ntfy priority value is not a number [VIGILANT_NOTIFICATION_NTFY_TOPIC]');
            }

            $this->config['notification_ntfy_priority'] = (int) $this->getEnv('NOTIFICATION_NTFY_PRIORITY');
        }
    }

    /**
     * Validate `VIGILANT_NOTIFICATION_NTFY_TOPIC`
     *
     * @throws ConfigException if no ntfy topic is given
     */
    public function topic(): void
    {
        if ($this->hasEnv('NOTIFICATION_NTFY_TOPIC') === false) {
            throw new ConfigException('No ntfy topic given [VIGILANT_NOTIFICATION_NTFY_TOPIC]');
        }

        $this->config['notification_ntfy_topic'] = $this->getEnv('NOTIFICATION_NTFY_TOPIC');
    }

    /**
     * Validate `NOTIFICATION_NTFY_TOKEN`
     *
     * @throws ConfigException if no Ntfy auth token is given
     */
    public function token(): void
    {
        if ($this->hasEnv('NOTIFICATION_NTFY_TOKEN') === false) {
            throw new ConfigException('No ntfy authentication token given [VIGILANT_NOTIFICATION_NTFY_TOKEN]');
        }

        $this->config['notification_ntfy_token'] = $this->getEnv('NOTIFICATION_NTFY_TOKEN');
    }
}
