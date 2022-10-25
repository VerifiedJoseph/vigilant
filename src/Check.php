<?php

namespace Vigilant;

use Vigilant\Config;
use Vigilant\Notification\Gotify as Gotify;

use SimplePie\SimplePie;

use Exception;

final class Check
{
    /**
     * @var FeedDetails $details Feed details (name, url, interval and hash)
     */
    private FeedDetails $details;

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
     */
    public function __construct(FeedDetails $details)
    {
        $this->details = $details;
        $this->cache = new Cache($this->details->getHash());
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
                throw new Exception($feed->error());
            }

            $itemHashes = [];

            foreach ($feed->get_items() as $item) {
                $hash = sha1($item->get_permalink());
                $itemHashes[] = $hash;

                if (in_array($hash, $this->cache->getItems()) === false) {
                    Output::text('Found...' . html_entity_decode($item->get_title()) . ' (' . $hash . ')');

                    if ($this->firstCheck === false) {
                        $config = [
                            'title' => html_entity_decode($item->get_title()),
                            'message' => strip_tags(html_entity_decode($item->get_description())),
                            'url' => $item->get_permalink()
                        ];

                        if (Config::get('NOTIFICATION_SERVICE') === 'nfty') {
                            $config['priority'] = Config::get('NOTIFICATION_NFTY_PRIORITY');
                            $config['topic'] = Config::get('NOTIFICATION_NFTY_TOPIC');
                        }

                        if (Config::get('NOTIFICATION_SERVICE') === 'gotify') {
                            $config['priority'] = Config::get('NOTIFICATION_GOTIFY_PRIORITY');
                        }

                        $this->notify($config);
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
     * @param array $config Notification config
     */
    private function notify(array $config): void
    {
        $notification = new Gotify();

        /*if (Config::get('NOTIFICATION_SERVICE') === 'nfty') {
            $notification = new Nfty();
        }*/

        $notification->config($config);
        $notification->send();
    }
}
