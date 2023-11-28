<?php

namespace Vigilant;

use Vigilant\Config;
use Vigilant\Notification\Gotify;
use Vigilant\Notification\Ntfy;
use Vigilant\Exception\CheckException;
use Vigilant\Exception\NotificationException;

final class Check
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var Feed\Details $details Feed details (name, url, interval and hash)
     */
    private Feed\Details $details;

    /**
     * @var Cache $cache Cache class object
     */
    private Cache $cache;

    /**
     * @var bool $checkError Check error status
     */
    private bool $checkError = false;

    /**
     * Constructor
     *
     * @param Feed\Details $details Feed details
     * @param Config $config
     */
    public function __construct(Feed\Details $details, Config $config)
    {
        $this->details = $details;
        $this->config = $config;

        $this->cache = new Cache(
            $this->config->getCachePath(),
            $this->details->getHash()
        );
    }

    /**
     * Run check
     */
    public function run(): void
    {
        if ($this->cache->isExpired() === true) {
            try {
                Output::text('Checking...' . $this->details->getName() . ' (' . $this->details->getUrl() . ')');

                $result = $this->fetch(
                    $this->details->getUrl()
                );

                $this->process($result);
                $this->cache->resetErrorCount();
            } catch (CheckException $err) {
                Output::text($err->getMessage());

                $this->cache->increaseErrorCount();

                if ($this->cache->getErrorCount() >= 4) {
                    $this->errorNotify(
                        title: '[Vigilant] Error when fetching ' . $this->details->getName(),
                        message: $err->getMessage()
                    );

                    $this->cache->resetErrorCount();
                }
            } catch (NotificationException $err) {
                Output::text($err->getMessage());
            } finally {
                if ($this->checkError === false) {
                    $this->cache->resetErrorCount();

                    if ($this->cache->isFirstCheck() === true) {
                        $this->cache->setFirstCheck();
                        $this->cache->setFeedUrl($this->details->getUrl());
                    }
                }

                $this->cache->updateNextCheck($this->details->getInterval());
                $this->cache->save();

                $when = date('Y-m-d H:i:s', $this->cache->getNextCheck());
                Output::text('Next check in ' . $this->details->getInterval() . ' seconds at ' . $when);
            }
        }
    }

    /**
     * Fetch feed
     *
     * @param string $url Feed URL
     * @return \FeedIo\Reader\Result
     *
     * @throws CheckException
     */
    private function fetch(string $url): \FeedIo\Reader\Result
    {
        try {
            $client = new \FeedIo\Adapter\Http\Client(new \GuzzleHttp\Client());
            $feedIo = new \FeedIo\FeedIo($client);

            return $feedIo->read($url);
        } catch (\FeedIo\Reader\ReadErrorException $err) {
            $this->checkError = true;

            switch ($err->getMessage()) {
                case 'not found':
                case 'internal server error':
                    $message = 'Failed to fetch: ' . $url . ' (returned ' . $err->getMessage() . ')';
                    break;
                default:
                    $message = 'Failed to parse feed (' . $err->getMessage() . ')';
                    break;
            }

            throw new CheckException($message);
        }
    }

    /**
     * Process fetched feed
     *
     * @param \FeedIo\Reader\Result $result
     */
    private function process(\FeedIo\Reader\Result $result): void
    {
        $itemHashes = [];
        $newItems = 0;

        foreach ($result->getFeed() as $item) {
            $hash = sha1($item->getLink());
            $itemHashes[] = $hash;

            if (in_array($hash, $this->cache->getItems()) === false) {
                $newItems += 1;

                Output::text('Found...' . html_entity_decode($item->getTitle()) . ' (' . $hash . ')');

                if ($this->cache->isFirstCheck() === false) {
                    $this->notify(
                        title: html_entity_decode($item->getTitle()),
                        message: strip_tags(html_entity_decode($item->getContent())),
                        url: $item->getLink()
                    );
                }
            }
        }

        Output::text('Found ' . $newItems . ' new item(s).');

        $this->cache->updateItems($itemHashes);

        if ($newItems > 0 && $this->cache->isFirstCheck() === true) {
            Output::text('First feed check, not sending notifications for found items.');
        }
    }

    /**
     * Send a notification
     *
     * @param string $title Notification title
     * @param string $message Notification message
     * @param string $url Notification URL
     */
    private function notify(string $title, string $message, string $url): void
    {
        $config = [];
        $config['title'] = $title;
        $config['message'] = $message;
        $config['url'] = $url;

        switch ($this->config->getNotificationService()) {
            case 'ntfy':
                $notification = new Ntfy();

                $config['server'] = $this->config->getNtfyUrl();
                $config['topic'] = $this->config->getNtfyTopic();
                $config['priority'] = $this->config->getNtfyPriority();

                if ($this->config->getNtfyAuth() === true) {
                    $config['auth'] = [
                        'username' => $this->config->getNtfyUsername(),
                        'password' => $this->config->getNtfyPassword()
                    ];
                }

                if ($this->details->hasNtfyTopic() === true) {
                    $config['topic'] = $this->details->getNtfyTopic();
                }

                if ($this->details->hasNtfyPriority() === true) {
                    $config['priority'] = $this->details->getNtfyPriority();
                }
                break;
            default:
                $notification = new Gotify();

                $config['server'] = $this->config->getGotifyUrl();
                $config['priority'] = $this->config->getGotifyPriority();
                $config['token'] = $this->config->getGotifyToken();

                if ($this->details->hasGotifyToken() === true) {
                    $config['token'] = $this->details->getGotifyToken();
                }

                if ($this->details->hasGotifyPriority() === true) {
                    $config['priority'] = $this->details->getGotifyPriority();
                }
        }

        $notification->config($config);
        $notification->send();
    }

    /**
     * Send a error notification
     *
     * @param string $title Notification title
     * @param string $message Notification message
     */
    private function errorNotify(string $title, string $message): void
    {
        try {
            $this->notify(
                title: $title,
                message: $message,
                url: ''
            );
        } catch (NotificationException $err) {
            Output::text($err->getMessage());
        }
    }
}
