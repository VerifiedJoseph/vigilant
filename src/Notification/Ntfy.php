<?php

namespace Vigilant\Notification;

use Vigilant\Output;
use Vigilant\Notification\Notification;
use Vigilant\Exception\NotificationException;
use Ntfy\Auth;
use Ntfy\Client;
use Ntfy\Server;
use Ntfy\Message;
use Ntfy\Exception\NtfyException;
use Ntfy\Exception\EndpointException;

/**
 * Send notifications using `VerifiedJoseph/ntfy-php-library`
 */
final class Ntfy extends Notification
{
    /** @inheritdoc */
    public function send(string $title, string $body, string $url): void
    {
        try {
            // Set server
            $server = new Server($this->config['server']);

            // Create a new message
            $message = new Message();
            $message->topic($this->config['topic']);
            $message->title($title);
            $message->body($body);
            $message->priority($this->config['priority']);
            $message->clickAction($url);

            $auth = null;
            if ($this->config['auth']['method'] === 'password') {
                $auth = new Auth\User(
                    $this->config['auth']['username'],
                    $this->config['auth']['password']
                );
            }

            if ($this->config['auth']['method'] === 'token') {
                $auth = new Auth\Token($this->config['auth']['token']);
            }

            $client = new Client($server, $auth);
            $client->send($message);

            Output::text('Sent notification using Ntfy');
        } catch (EndpointException | NtfyException $err) {
            throw new NotificationException('[Ntfy] ' . $err->getMessage());
        }
    }
}
