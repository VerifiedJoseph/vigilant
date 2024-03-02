<?php

namespace Vigilant;

use Vigilant\Config;
use Vigilant\Exception\CheckException;
use Vigilant\Exception\NotificationException;
use Vigilant\Notification\Notification;

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
     * @var Notification $notification Notification class instance
     */
    private Notification $notification;

    /**
     * @var Cache $cache Cache class instance
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
     * @param Config $config Script config
     */
    public function __construct(Feed\Details $details, Config $config)
    {
        $this->details = $details;
        $this->config = $config;

        $notify = new Notify($details, $config);
        $this->notification = $notify->getClass();

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
                        '[Vigilant] Error when fetching ' . $this->details->getName(),
                        $err->getMessage()
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
            $client = new \FeedIo\Adapter\Http\Client(new \GuzzleHttp\Client([
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; rv:123.0) Gecko/20100101 Firefox/123.0',
                    'Accept' => '*/*'
                ]]));
             $feedIo = new \FeedIo\FeedIo($client);

             return $feedIo->read($url);
        } catch (\FeedIo\Reader\ReadErrorException $err) {
            $this->checkError = true;

            /** @var \FeedIo\Adapter\ServerErrorException $serverErr */
            $serverErr = $err->getPrevious();

            switch ($err->getMessage()) {
                case 'not found':
                case 'internal server error':
                    $message = sprintf(
                        'Failed to fetch: %s (%s %s)',
                        $url,
                        $serverErr->getResponse()->getStatusCode(),
                        $serverErr->getResponse()->getReasonPhrase()
                    );
                    break;
                default:
                    $message = sprintf('Failed to parse feed (%s)', $err->getMessage());
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
                    $this->notification->send(
                        title: html_entity_decode($item->getTitle()),
                        body: strip_tags(html_entity_decode($item->getContent())),
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
     * Send an error notification
     *
     * @param string $title Notification title
     * @param string $body Notification body
     */
    private function errorNotify(string $title, string $body): void
    {
        try {
            $this->notification->send($title, $body);
        } catch (NotificationException $err) {
            Output::text($err->getMessage());
        }
    }
}
