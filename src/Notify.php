<?php

namespace Vigilant;

use Vigilant\Notification\Notification;
use Vigilant\Notification\Gotify;
use Vigilant\Notification\Ntfy;

class Notify
{
    private Notification $class;

    /**
     * Create and config Notification class
     *
     * @param Feed\Details $details Feed details
     * @param Config $config Script config
     */
    public function __construct(Feed\Details $details, Config $config)
    {
        if ($config->getNotificationService() === 'gotify') {
            $this->class = $this->createGotify($details, $config);
        } else {
            $this->class = $this->createNtfy($details, $config);
        }
    }

    /**
     * Returns Notification class instance
     */
    public function getClass(): Notification
    {
        return $this->class;
    }

    /**
     * Create and config Gotify instance
     *
     * @param Feed\Details $details Feed details
     * @param Config $config Script config
     */
    private function createGotify(Feed\Details $details, Config $config): Gotify
    {
        $gotifyConfig = [
            'server' => $config->getGotifyUrl(),
            'priority' => $config->getGotifyPriority(),
            'token' => $config->getGotifyToken()
        ];

        if ($details->hasGotifyPriority() === true) {
            $gotifyConfig['priority'] = $details->getGotifyPriority();
        }

        if ($details->hasGotifyToken() === true) {
            $gotifyConfig['token'] = $details->getGotifyToken();
        }

        return new Gotify($gotifyConfig);
    }

    /**
     * Create and config ntfy instance
     *
     * @param Feed\Details $details Feed details
     * @param Config $config Script config
     */
    private function createNtfy(Feed\Details $details, Config $config): Ntfy
    {
        $ntfyConfig = [
            'server' => $config->getNtfyUrl(),
            'topic' => $config->getNtfyTopic(),
            'priority' => $config->getNtfyPriority(),
            'auth' => [
                'method' => $config->getNtfyAuthMethod()
            ]
        ];

        if ($config->getNtfyAuthMethod() === 'password') {
            $ntfyConfig['auth']['username'] = $config->getNtfyUsername();
            $ntfyConfig['auth']['password'] = $config->getNtfyPassword();
        } elseif ($config->getNtfyAuthMethod() === 'token') {
            $ntfyConfig['auth']['token'] = $config->getNtfyToken();
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

        return new Ntfy($ntfyConfig);
    }
}
