<?php

declare(strict_types=1);

namespace Vigilant\Notify;

use Vigilant\Config;
use Vigilant\Message;

class Payload
{
    /** @var Config Config class instance */
    private Config $config;

    /**
     * @param Config $config Config class instance
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Get message payload
     * @param Message $message
     * @param array<string, mixed> $serviceConfig
     * @return array<string, mixed>
     */
    public function get(Message $message, array $serviceConfig): array
    {
        if ($this->config->getNotificationService() === 'gotify') {
            return [
                'title' => $message->getTitle(),
                'message' => $message->getBody(),
                'priority' => $serviceConfig['priority'],
                'extras' => [
                    'client::notification' => [
                        'click' => ['url' => $message->getUrl()]
                    ]
                ]
            ];
        } else {
            return [
                'topic' => $serviceConfig['topic'],
                'title' => $message->getTitle(),
                'message' => $message->getBody(),
                'markdown' => false,
                'priority' => $serviceConfig['priority'],
                'click' => $message->getUrl(),
            ];
        }
    }
}
