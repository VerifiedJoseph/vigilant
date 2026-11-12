<?php

namespace Vigilant\Notify;

use Vigilant\Config;
use Vigilant\Feed;

class ServiceConfig
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function get(Feed\Details $details): array
    {
        if ($this->config->getNotificationService() === 'gotify') {
            return $this->createGotify($details);
        } else {
            return $this->createNtfy($details);
        }
    }

    private function createGotify(Feed\Details $details): array
    {
        $config = [
            'server' => $this->config->getGotifyUrl(),
            'priority' => $this->config->getGotifyPriority(),
            'token' => $this->config->getGotifyToken()
        ];

        if ($details->getGotifyUrl() !== null) {
            $config['server'] = $details->getGotifyUrl();
        }

        if ($details->getGotifyPriority() !== null) {
            $config['priority'] = $details->getGotifyPriority();
        }

        if ($details->getGotifyToken() !== null) {
            $config['token'] = $details->getGotifyToken();
        }

        return $config;
    }

    private function createNtfy(Feed\Details $details): array
    {
        $config = [
            'server' => $this->config->getNtfyUrl(),
            'topic' => $this->config->getNtfyTopic(),
            'priority' => $this->config->getNtfyPriority(),
            'token' => $this->config->getNtfyToken()
        ];

        if ($details->getNtfyUrl() !== null) {
            $config['server'] = $details->getNtfyUrl();
        }

        if ($details->getNtfyToken() !== null) {
            $config['token'] = $details->getNtfyToken();
        }

        if ($details->getNtfyTopic() !== null) {
            $config['topic'] = $details->getNtfyTopic();
        }

        if ($details->getNtfyPriority() !== null) {
            $config['priority'] = $details->getNtfyPriority();
        }

        return $config;
    }
}
