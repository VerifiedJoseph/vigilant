<?php

declare(strict_types=1);

namespace Vigilant;

use FeedIo\FeedIo;
use FeedIo\Reader\Result;
use Vigilant\Exception\FetchException;

class Fetch
{
    private \FeedIo\Adapter\Http\Client $client;

    /** @var array<string, string> $headers HTTP headers */
    private array $headers = [
        'Accept' => '*/*'
    ];

    /**
     * @param string $userAgent Default user-agent string or from environment variable `VIGILANT_USER_AGENT`
     * @param null|\GuzzleHttp\Client $httpClient Custom GuzzleHttp client
     */
    public function __construct(string $userAgent, ?\GuzzleHttp\Client $httpClient = null)
    {
        $this->headers['User-Agent'] = $userAgent;

        if ($httpClient === null) {
            $httpClient = new \GuzzleHttp\Client(['headers' => $this->headers]);
        }

        $this->client = new \FeedIo\Adapter\Http\Client($httpClient);
    }

    /**
     * Get RSS feed
     *
     * @param string $url Feed URL
     * @param string $useragent User agent string
     * @return \FeedIo\Reader\Result
     *
     * @throws FetchException if request failed
     */
    public function get(string $url, ?string $useragent = null): Result
    {
        try {
            $client = $this->client;

            if ($useragent !== null) {
                $headers = $this->headers;
                $headers['User-Agent'] = $useragent;

                $client = new \FeedIo\Adapter\Http\Client(
                    new \GuzzleHttp\Client($headers)
                );
            }

            return (new \FeedIo\FeedIo($client))->read($url);
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
