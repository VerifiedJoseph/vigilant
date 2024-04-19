<?php

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
     * Validate `VIGILANT_NOTIFICATION_AUTH`
     *
     * @throws ConfigException if an unknown Ntfy auth type is given
     */
    public function auth(): void
    {
        if ($this->hasEnv('NOTIFICATION_NTFY_AUTH') === true) {
            if (in_array($this->getEnv('NOTIFICATION_NTFY_AUTH'), ['password', 'token']) === false) {
                throw new ConfigException('Unknown ntfy authentication method given [VIGILANT_NOTIFICATION_NTFY_USERNAME]');
            }

            $this->config['notification_ntfy_auth'] = $this->getEnv('NOTIFICATION_NTFY_AUTH');

            if ($this->config['notification_ntfy_auth'] === 'password') {
                $this->username();
                $this->password();
            }

            if ($this->config['notification_ntfy_auth'] === 'token') {
                $this->token();
            }
        }
    }

    /**
     * Validate `NOTIFICATION_NTFY_USERNAME`
     *
     * @throws ConfigException if no Ntfy auth username is given
     */
    private function username(): void
    {
        if ($this->hasEnv('NOTIFICATION_NTFY_USERNAME') === false) {
            throw new ConfigException('No ntfy authentication username given [VIGILANT_NOTIFICATION_NTFY_USERNAME]');
        }

        $this->config['notification_ntfy_username'] = $this->getEnv('NOTIFICATION_NTFY_USERNAME');
    }

    /**
     * Validate `NOTIFICATION_NTFY_PASSWORD`
     *
     * @throws ConfigException if no Ntfy auth password is given
     */
    private function password(): void
    {
        if ($this->hasEnv('NOTIFICATION_NTFY_PASSWORD') === false) {
            throw new ConfigException('No ntfy authentication password given [VIGILANT_NOTIFICATION_NTFY_PASSWORD]');
        }

        $this->config['notification_ntfy_password'] = $this->getEnv('NOTIFICATION_NTFY_PASSWORD');
    }

    /**
     * Validate `NOTIFICATION_NTFY_TOKEN`
     *
     * @throws ConfigException if no Ntfy auth token is given
     */
    private function token(): void
    {
        if ($this->hasEnv('NOTIFICATION_NTFY_TOKEN') === false) {
            throw new ConfigException('No ntfy authentication token given [VIGILANT_NOTIFICATION_NTFY_TOKEN]');
        }

        $this->config['notification_ntfy_token'] = $this->getEnv('NOTIFICATION_NTFY_TOKEN');
    }
}
