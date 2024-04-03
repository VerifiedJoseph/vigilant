<?php

namespace Vigilant;

use Vigilant\Config;
use Vigilant\Exception\FetchException;

final class Check
{
    /** @var Config */
    private Config $config;

    /** @var Fetch */
    private Fetch $fetch;

    /** @var Feed\Details $details Feed details (name, url, interval and hash) */
    private Feed\Details $details;

    /** @var Cache $cache Cache class instance */
    private Cache $cache;

    /** @var bool $checkError Check error status */
    private bool $checkError = false;

    /** @var array<int, Message> $messages */
    private array $messages = [];

    /**
     * @param Feed\Details $details Feed details
     * @param Config $config Script config
     * @param Fetch $fetch Fetch class instance
     */
    public function __construct(Feed\Details $details, Config $config, Fetch $fetch)
    {
        $this->details = $details;
        $this->config = $config;
        $this->fetch = $fetch;

        $this->cache = new Cache(
            $this->config->getCachePath(),
            $this->details->getHash()
        );
    }

    /**
     * Returns boolean indicating if a check is due
     * @return bool
     */
    public function isDue(): bool
    {
        return $this->cache->isExpired();
    }

    /**
     * Returns messages created when checking feeds
     * @return array<int, Message>
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Returns next check date in readable format (`Y-m-d H:i:s`)
     * @return string
     */
    public function getNextCheckDate(): string
    {
        return date('Y-m-d H:i:s', $this->cache->getNextCheck());
    }

    public function check(): void
    {
        try {
            Output::text('Checking...' . $this->details->getName() . ' (' . $this->details->getUrl() . ')');

            $result = $this->fetch->get($this->details->getUrl());

            $this->process($result);
            $this->cache->resetErrorCount();
        } catch (FetchException $err) {
            Output::text($err->getMessage());

            $this->cache->increaseErrorCount();

            if ($this->cache->getErrorCount() >= 4) {
                $this->messages[] = new Message(
                    '[Vigilant] Error when fetching ' . $this->details->getName(),
                    $err->getMessage()
                );

                $this->cache->resetErrorCount();
            }
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
                    $this->messages[] = new Message(
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
}
