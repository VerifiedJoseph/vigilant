<?php

namespace Vigilant;

use FeedIo\FeedIo;
use FeedIo\Reader\Result;
use Vigilant\Exception\FetchException;

class Fetch
{
    private FeedIo $feedIo;

    /** @var array<string, string> $headers HTTP headers */
    private array $headers = [
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; rv:123.0) Gecko/20100101 Firefox/123.0',
        'Accept' => '*/*'
    ];

    public function __construct()
    {
        $client = new \FeedIo\Adapter\Http\Client(
            new \GuzzleHttp\Client(['headers' => $this->headers])
        );

        $this->feedIo = new \FeedIo\FeedIo($client);
    }

    /**
     * Get RSS feed
     *
     * @param string $url Feed URL
     * @return \FeedIo\Reader\Result
     *
     * @throws FetchException if request failed
     */
    public function get(string $url): Result
    {
        try {
            return $this->feedIo->read($url);
        } catch (\FeedIo\Reader\ReadErrorException $err) {
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

            throw new FetchException($message);
        }
    }
}
