<?php

namespace Vigilant;

use FeedIo\FeedIo;
use FeedIo\Reader\Result;
use GuzzleHttp\Client;
use Vigilant\Exception\FetchException;

class Fetch
{
    private FeedIo $feedIo;

    /** @var array<string, string> $headers HTTP headers */
    private array $headers = [
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; rv:127.0) Gecko/20100101 Firefox/127.0',
        'Accept' => '*/*'
    ];

    /**
     * @param null|Client $httpClient Custom GuzzleHttp client
     */
    public function __construct(?\GuzzleHttp\Client $httpClient = null)
    {
        if (is_null($httpClient) === true) {
            $httpClient = new \GuzzleHttp\Client(['headers' => $this->headers]);
        }

        $client = new \FeedIo\Adapter\Http\Client($httpClient);
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
            switch ($err->getMessage()) {
                case 'not found':
                    $message = sprintf(
                        'Failed to fetch: %s (404 Not Found)',
                        $url,
                    );
                    break;
                case 'internal server error':
                    /** @var \FeedIo\Adapter\ServerErrorException $serverErr */
                    $serverErr = $err->getPrevious();

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
