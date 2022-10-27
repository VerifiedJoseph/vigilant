<?php

namespace Vigilant;

use Vigilant\Config;
use Vigilant\Notification\Gotify;
use Vigilant\Notification\Ntfy;
use Vigilant\Exception\CheckException;

use SimplePie\SimplePie;

final class Check
{
    /**
     * @var Feed\Details $details Feed details (name, url, interval and hash)
     */
    private Feed\Details $details;

    /**
     * @var Cache $cache Cache class object
     */
    private Cache $cache;

    /**
     * @var bool $firstCheck Feed first check status
     */
    private bool $firstCheck = true;

    /**
     * Constructor
     *
     * @param Feed\Details $details Feed details
     */
    public function __construct(Feed\Details $details)
    {
        $this->details = $details;

        $this->cache = new Cache(
            Config::getCachePath(),
            $this->details->getHash()
        );
    }

    /**
     * Run check
     */
    public function run(): void
    {
        if ($this->cache->getFirstCheck() !== 0) {
            $this->firstCheck = false;
        }

        if ($this->cache->isExpired() === true) {
            Output::text('Checking...' . $this->details->getName() . ' (' . $this->details->getUrl() . ')');

            if ($this->firstCheck === true) {
                Output::text('First feed check, not sending notifications.');
            }

            $feed = new SimplePie();
            $feed->set_feed_url($this->details->getUrl());
            $feed->set_cache_duration(60);
            $feed->set_cache_location(Config::getSimplePieCachePath());
            $feed->init();
            $feed->handle_content_type();

            if ($feed->error()) {
                throw new CheckException($feed->error);
            }

            $itemHashes = [];

            foreach ($feed->get_items() as $item) {
                $hash = sha1($item->get_permalink());
                $itemHashes[] = $hash;

                if (in_array($hash, $this->cache->getItems()) === false) {
                    Output::text('Found...' . html_entity_decode($item->get_title()) . ' (' . $hash . ')');

                    if ($this->firstCheck === false) {
                        $this->notify(
                            title: html_entity_decode($item->get_title()),
                            message: strip_tags(html_entity_decode($item->get_description())),
                            url: $item->get_permalink()
                        );
                    }
                }
            }

            if ($this->firstCheck === true) {
                $this->cache->setFirstCheck();
                $this->cache->setFeedUrl($this->details->getUrl());
            }

            $this->cache->updateItems($itemHashes);
            $this->cache->updateNextCheck($this->details->getInterval());
            $this->cache->save();
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

        switch (Config::get('NOTIFICATION_SERVICE')) {
            case 'ntfy':
                $notification = new Ntfy();

                $config['topic'] = $this->details->getNtfyTopic();
                $config['priority'] = $this->details->getNtfyPriority();
                break;
            default:
                $notification = new Gotify();

                $config['token'] = $this->details->getGotifyToken();
                $config['priority'] = $this->details->getGotifyPriority();
        }

        $notification->config($config);
        $notification->send();
    }
}
