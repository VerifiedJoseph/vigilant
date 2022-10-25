<?php

namespace Vigilant\Notification;

use Vigilant\Config;

use Gotify\Server;
use Gotify\Auth\Token;
use Gotify\Endpoint\Message;
use Gotify\Exception\GotifyException;
use Gotify\Exception\EndpointException;

use Exception;

final class Gotify extends Base
{
    /**
     * Send notification using `verifiedjoseph/gotify-api-php`
     */
    public function send(): void
    {
        try {
            // Set server
            $server = new Server(Config::get('NOTIFICATION_GOTIFY_URL'));

            // Set application token
            $auth = new Token($this->config['token']);

            // Create Message class instance
            $message = new Message($server, $auth);

            // Send message
            $message->create(
                title: $this->config['title'],
                message: $this->config['message'],
                priority: $this->config['priority'],
            );
        } catch (EndpointException | GotifyException $err) {
            throw new Exception('[Gotify error] ' . $err->getMessage());
        }
    }
}
