<?php

namespace Vigilant;

use DateTime;
use DateTimeZone;
use Vigilant\Config;
use Vigilant\Exception\FetchException;

final class Check
{
    /** @var Feed\Details $details Feed details (name, url, interval and hash) */
    private Feed\Details $details;

    /** @var Fetch Fetch class instance */
    private Fetch $fetch;

    /** @var Config Config class instance */
    private Config $config;

    /** @var Logger Logger class instance */
    private Logger $logger;

    /** @var Cache $cache Cache class instance */
    private Cache $cache;

    /** @var bool $checkError Check error status */
    private bool $checkError = false;

    /** @var array<int, Message> $messages */
    private array $messages = [];

    /**
     * @param Feed\Details $details Feed details
     * @param Config $config Config class instance
     * @param Fetch $fetch Fetch class instance
     * @param Logger $logger Logger class instance
     */
    public function __construct(Feed\Details $details, Fetch $fetch, Config $config, Logger $logger)
    {
        $this->details = $details;
        $this->fetch = $fetch;
        $this->config = $config;
        $this->logger = $logger;

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
        $date = new DateTime();
        $date->setTimestamp($this->cache->getNextCheck());
        $date->setTimezone(new DateTimeZone($this->config->getTimezone()));

        return $date->format('Y-m-d H:i:s');
    }

    public function check(): void
    {
        try {
            $this->logger->info(sprintf(
                'Checking...%s (%s)',
                $this->details->getName(),
                $this->details->getUrl()
            ));

            $result = $this->fetch->get($this->details->getUrl());

            $this->process($result);
            $this->cache->resetErrorCount();
        } catch (FetchException $err) {
            $this->logger->error($err->getMessage());

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

                $title = html_entity_decode($item->getTitle());
                $body = strip_tags(html_entity_decode($item->getContent()));

                $this->logger->info(sprintf('Found...%s (%s)', $title, $hash));

                if ($this->cache->isFirstCheck() === false) {
                    $this->messages[] = new Message(
                        $title,
                        $body,
                        $item->getLink(),
                        $this->details->getTitlePrefix()
                    );
                }
            }
        }

        $this->logger->info(sprintf('Found %s new item(s).', $newItems));

        $this->cache->updateItems($itemHashes);

        if ($newItems > 0 && $this->cache->isFirstCheck() === true) {
            $this->logger->info('First feed check, not sending notifications for found items.');
        }
    }
}
