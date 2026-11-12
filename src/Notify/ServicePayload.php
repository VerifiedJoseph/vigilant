<?php

declare(strict_types=1);

namespace Vigilant\Notify;

use Vigilant\Config;
use Vigilant\Message;

class ServicePayload
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

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
