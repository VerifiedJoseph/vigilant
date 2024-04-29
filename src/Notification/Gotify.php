<?php

namespace Vigilant\Notification;

use Vigilant\Notification\AbstractNotification;
use Vigilant\Exception\NotificationException;
use Gotify\Server;
use Gotify\Auth\Token;
use Gotify\Endpoint\Message;
use Gotify\Exception\GotifyException;
use Gotify\Exception\EndpointException;

/**
 * Send notifications using `verifiedjoseph/gotify-api-php`
 */
final class Gotify extends AbstractNotification
{
    /** @var Message $message */
    private Message $message;

    /** @inheritdoc */
    public function send(string $title, string $body, string $url = ''): void
    {
        try {
            // Send message
            $this->message->create(
                title: $title,
                message: $body,
                priority: $this->config['priority'],
                extras: [
                    'client::notification' => [
                        'click' => ['url' => $url]
                    ]
                ]
            );

            $this->logger->info('Sent notification using Gotify');
        } catch (EndpointException | GotifyException $err) {
            throw new NotificationException('[Gotify] ' . $err->getMessage());
        }
    }

    /** @inheritdoc */
    protected function setup(): void
    {
        $this->message = new Message(
            new Server($this->config['server']),
            new Token($this->config['token'])
        );
    }
}
