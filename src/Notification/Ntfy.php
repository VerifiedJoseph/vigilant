<?php

namespace Vigilant\Notification;

use Vigilant\Config;
use Vigilant\Notification;

use Ntfy\Client;
use Ntfy\Server;
use Ntfy\Message;

use Ntfy\Exception\NtfyException;
use Ntfy\Exception\EndpointException;

use Exception;

final class Ntfy extends Notification
{
    /**
     * Send notification using `VerifiedJoseph/ntfy-php-library`
     */
    public function send(): void
    {
        try {
            // Set server
            $server = new Server(Config::get('NOTIFICATION_NTFY_URL'));

            // Create a new message
            $message = new Message();
            $message->topic($this->config['topic']);
            $message->title($this->config['title']);
            $message->body($this->config['message']);
            $message->priority($this->config['priority']);

            $client = new Client($server);
            $client->send($message);
        } catch (EndpointException | NtfyException $err) {
            throw new Exception('[Ntfy error] ' . $err->getMessage());
        }
    }
}
