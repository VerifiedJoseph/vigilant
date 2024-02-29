<?php

namespace Vigilant\Notification;

use Vigilant\Output;
use Vigilant\Notification\Notification;
use Vigilant\Exception\NotificationException;
use Gotify\Server;
use Gotify\Auth\Token;
use Gotify\Endpoint\Message;
use Gotify\Exception\GotifyException;
use Gotify\Exception\EndpointException;

/**
 * Send notifications using `verifiedjoseph/gotify-api-php`
 */
final class Gotify extends Notification
{
    /** @inheritdoc */
    public function send(string $title, string $body, string $url): void
    {
        try {
            // Set server
            $server = new Server($this->config['server']);

            // Set application token
            $auth = new Token($this->config['token']);

            // Create Message class instance
            $message = new Message($server, $auth);

            // Send message
            $message->create(
                title: $title,
                message: $body,
                priority: $this->config['priority'],
                extras: [
                    'client::notification' => [
                        'click' => ['url' => $url]
                    ]
                ]
            );

            Output::text('Sent notification using Gotify');
        } catch (EndpointException | GotifyException $err) {
            throw new NotificationException('[Gotify] ' . $err->getMessage());
        }
    }
}
