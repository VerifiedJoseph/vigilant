<?php

declare(strict_types=1);

namespace Vigilant\Notification;

use Vigilant\Notification\AbstractNotification;
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
final class Ntfy extends AbstractNotification
{
    /** @var Server $server */
    private Server $server;

    /** @var Client $client */
    private Client $client;

    /** @inheritdoc */
    public function send(string $title, string $body, string $url = ''): void
    {
        try {
            // Create a new message
            $message = new Message();
            $message->topic($this->config['topic']);
            $message->title($title);
            $message->body($body);
            $message->priority($this->config['priority']);
            $message->clickAction($url);

            $this->client->send($message);

            $this->logger->info('Sent notification using Ntfy');
        } catch (EndpointException | NtfyException $err) {
            throw new NotificationException('[Ntfy] ' . $err->getMessage());
        }
    }

    /** @inheritdoc */
    protected function setup(): void
    {
        $this->server = new Server($this->config['server']);

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

        $this->client = new Client($this->server, $auth);
    }
}
