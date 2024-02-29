<?php

namespace Vigilant\Notification;

abstract class Notification
{
    /**
     * @var array<string, mixed> $config Notification config
      */
    protected array $config = [];

    /**
     * Set notification config
     *
     * @param array<string, mixed> $config
     */
    public function config(array $config): void
    {
        $this->config = $config;
    }

    /**
     * Send notification
     *
     * @param string $title Message title
     * @param string $body Message body
     * @param string $url Message url
     */
    abstract public function send(string $title, string $body, string $url): void;
}
