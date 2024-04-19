<?php

namespace Vigilant\Config\Validate;

use Vigilant\Config\AbstractValidator;
use Vigilant\Exception\ConfigException;

class Gotify extends AbstractValidator
{
    /**
     * Validate `VIGILANT_NOTIFICATION_GOTIFY_URL`
     *
     * @throws ConfigException if no gotify URL is given
     */
    public function url(): void
    {
        if ($this->hasEnv('NOTIFICATION_GOTIFY_URL') === false) {
            throw new ConfigException('No gotify URL given [NOTIFICATION_GOTIFY_URL]');
        }

        $this->config['notification_gotify_url'] = $this->getEnv('NOTIFICATION_GOTIFY_URL');
    }

    /**
     * Validate `VIGILANT_NOTIFICATION_GOTIFY_PRIORITY`
     *
     * @throws ConfigException if priority is not a number
     */
    public function priority(): void
    {
        if ($this->hasEnv('NOTIFICATION_GOTIFY_PRIORITY') === true) {
            if (is_numeric($this->getEnv('NOTIFICATION_GOTIFY_PRIORITY')) === false) {
                throw new ConfigException('Gotify priority value is not a number [VIGILANT_NOTIFICATION_GOTIFY_PRIORITY]');
            }

            $this->config['notification_gotify_priority'] = (int) $this->getEnv('NOTIFICATION_GOTIFY_PRIORITY');
        }
    }

    /**
     * Validate `VIGILANT_NOTIFICATION_GOTIFY_TOKEN`
     *
     * @throws ConfigException if no auth token is given
     */
    public function token(): void
    {
        if ($this->hasEnv('NOTIFICATION_GOTIFY_TOKEN') === false) {
            throw new ConfigException('No gotify app token given [VIGILANT_NOTIFICATION_GOTIFY_TOKEN]');
        }

        $this->config['notification_gotify_token'] = $this->getEnv('NOTIFICATION_GOTIFY_TOKEN');
    }
}
