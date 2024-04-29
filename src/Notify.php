<?php

namespace Vigilant;

use Vigilant\Notification\Gotify;
use Vigilant\Notification\Ntfy;
use Vigilant\Notification\AbstractNotification;
use Vigilant\Exception\NotificationException;

class Notify
{
    private AbstractNotification $service;

    /** @var Config Config class instance */
    private Config $config;

    /** @var Logger Logger class instance */
    private Logger $logger;

    /**
     * Create and config Notification class
     *
     * @param Feed\Details $details Feed details
     * @param Config $config Config class instance
     * @param Logger $logger Logger class instance
     */
    public function __construct(Feed\Details $details, Config $config, Logger $logger)
    {
        $this->config = $config;
        $this->logger = $logger;

        if ($config->getNotificationService() === 'gotify') {
            $this->service = $this->createGotify($details);
        } else {
            $this->service = $this->createNtfy($details);
        }
    }

    /**
     * Send messages
     * @param array<int, Message> $messages
     */
    public function send(array $messages): void
    {
        foreach ($messages as $message) {
            try {
                $this->service->send(
                    $message->getTitle(),
                    $message->getBody(),
                    $message->getUrl()
                );
            } catch (NotificationException $err) {
                $this->logger->error($err->getMessage());
            }
        }
    }

    /**
     * Create and config Gotify instance
     *
     * @param Feed\Details $details Feed details
     */
    private function createGotify(Feed\Details $details): Gotify
    {
        $gotifyConfig = [
            'server' => $this->config->getGotifyUrl(),
            'priority' => $this->config->getGotifyPriority(),
            'token' => $this->config->getGotifyToken()
        ];

        if ($details->hasGotifyPriority() === true) {
            $gotifyConfig['priority'] = $details->getGotifyPriority();
        }

        if ($details->hasGotifyToken() === true) {
            $gotifyConfig['token'] = $details->getGotifyToken();
        }

        return new Gotify($gotifyConfig, $this->logger);
    }

    /**
     * Create and config ntfy instance
     *
     * @param Feed\Details $details Feed details
     */
    private function createNtfy(Feed\Details $details): Ntfy
    {
        $ntfyConfig = [
            'server' => $this->config->getNtfyUrl(),
            'topic' => $this->config->getNtfyTopic(),
            'priority' => $this->config->getNtfyPriority(),
            'auth' => [
                'method' => $this->config->getNtfyAuthMethod()
            ]
        ];

        if ($this->config->getNtfyAuthMethod() === 'password') {
            $ntfyConfig['auth']['username'] = $this->config->getNtfyUsername();
            $ntfyConfig['auth']['password'] = $this->config->getNtfyPassword();
        } elseif ($this->config->getNtfyAuthMethod() === 'token') {
            $ntfyConfig['auth']['token'] = $this->config->getNtfyToken();
        }

        if ($details->hasNtfyToken() === true) {
            $ntfyConfig['auth'] = [
                'method' => 'token',
                'token' => $details->getNtfyToken()
            ];
        }

        if ($details->hasNtfyTopic() === true) {
            $ntfyConfig['topic'] = $details->getNtfyTopic();
        }

        if ($details->hasNtfyPriority() === true) {
            $ntfyConfig['priority'] = $details->getNtfyPriority();
        }

        return new Ntfy($ntfyConfig, $this->logger);
    }
}
