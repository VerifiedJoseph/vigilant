<?php

declare(strict_types=1);

namespace Vigilant\Notify;

use Vigilant\Config;
use Vigilant\Message;

class Payload
{
    /** @var Config Config class instance */
    private Config $config;

    /** @var array<string, mixed> $serviceConfig */
    private array $serviceConfig;

    /**
     * @param array<string, mixed> $serviceConfig Notification service config
     * @param Config $config Config class instance
     */
    public function __construct(array $serviceConfig, Config $config)
    {
        $this->serviceConfig = $serviceConfig;
        $this->config = $config;
    }

    /**
     * Get message payload
     * @param Message $message
     * @return array<string, mixed>
     */
    public function get(Message $message): array
    {
        if ($this->config->getNotificationService() === 'gotify') {
            return [
                'title' => $message->getTitle(),
                'message' => $message->getBody(),
                'priority' => $this->serviceConfig['priority'],
                'extras' => [
                    'client::notification' => [
                        'click' => ['url' => $message->getUrl()]
                    ]
                ]
            ];
        } else {
            return [
                'topic' => $this->serviceConfig['topic'],
                'title' => $message->getTitle(),
                'message' => $message->getBody(),
                'markdown' => false,
                'priority' => $this->serviceConfig['priority'],
                'click' => $message->getUrl(),
            ];
        }
    }
}
