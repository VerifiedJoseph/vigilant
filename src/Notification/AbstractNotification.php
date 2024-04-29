<?php

namespace Vigilant\Notification;

use Vigilant\Logger;

abstract class AbstractNotification
{
    /** @var array<string, mixed> $config Notification config */
    protected array $config = [];

    /** @var Logger Logger class instance */
    protected Logger $logger;

    /**
     * @param array<string, mixed> $config Notification config
     * @param Logger $logger Logger class instance
     */
    public function __construct(array $config, Logger $logger)
    {
        $this->config = $config;
        $this->logger = $logger;

        $this->setup();
    }

    /**
     * Send notification
     *
     * @param string $title Message title
     * @param string $body Message body
     * @param string $url Message url
     */
    abstract public function send(string $title, string $body, string $url = ''): void;

    /**
     * Setup
     */
    abstract protected function setup(): void;
}
