<?php

namespace Vigilant\Notification;

use Vigilant\Config;
use Vigilant\Notification\Notification;
use Vigilant\Exception\NotificationException;
use Ntfy\Auth\User as Auth;
use Ntfy\Client;
use Ntfy\Server;
use Ntfy\Message;
use Ntfy\Exception\NtfyException;
use Ntfy\Exception\EndpointException;

final class Ntfy extends Notification
{
    /**
     * Send notification using `VerifiedJoseph/ntfy-php-library`
     */
    public function send(): void
    {
        try {
            // Set server
            $server = new Server($this->config['server']);

            // Create a new message
            $message = new Message();
            $message->topic($this->config['topic']);
            $message->title($this->config['title']);
            $message->body($this->config['message']);
            $message->priority($this->config['priority']);
            $message->clickAction($this->config['url']);

            $auth = null;
            if (isset($this->config['auth']) === true) {
                $auth = new Auth(
                    $this->config['auth']['username'],
                    $this->config['auth']['password']
                );
            }

            $client = new Client($server, $auth);
            $client->send($message);
        } catch (EndpointException | NtfyException $err) {
            throw new NotificationException('[Ntfy] ' . $err->getMessage());
        }
    }
}
