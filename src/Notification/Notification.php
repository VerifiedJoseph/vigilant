<?php

namespace Vigilant\Notification;

abstract class Notification
{
    /**
     * @var array<string, mixed> $config Notification config
      */
    protected array $config = [];

    /**
     * @param array<string, mixed> $config Notification config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->setup();
    }

    /**
     * Send notification
     *
     * @param string $title Message title
     * @param string $body Message body
     * @param string $url Message url
     */
    abstract public function send(string $title, string $body, string $url): void;

    /**
     * Setup
     */
    abstract protected function setup(): void;
}
