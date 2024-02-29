<?php

namespace Vigilant;

class Notify
{
    private Notification\Notification $class;

    /**
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
     * Send notification
     *
     * @param string $title Message title
     * @param string $body Message body
     * @param string $url Message url
     */
    public function send(string $title, string $body, string $url = ''): void
    {
        $this->class->send($title, $body, $url);
    }

    /**
     * Create and config Gotify instance
     *
     * @param Feed\Details $details Feed details
     * @param Config $config Script config
     */
    private function createGotify(Feed\Details $details, Config $config): Notification\Gotify
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

        return new Notification\Gotify($gotifyConfig);
    }

    /**
     * Create and config ntfy instance
     *
     * @param Feed\Details $details Feed details
     * @param Config $config Script config
     */
    private function createNtfy(Feed\Details $details, Config $config): Notification\Ntfy
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

        return new Notification\Ntfy($ntfyConfig);
    }
}
