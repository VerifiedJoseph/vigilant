<?php

declare(strict_types=1);

namespace Vigilant;

use FeedIo\Reader\Result;
use GuzzleHttp\RequestOptions;
use Vigilant\Helper\Json;
use Vigilant\Exception\RequestException;

class Request
{
    protected \GuzzleHttp\Client $httpClient;

    /** @var array<string, string> $headers HTTP headers */
    protected array $headers = [
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
            $this->httpClient = new \GuzzleHttp\Client(['headers' => $this->headers]);
        } else {
            $this->httpClient = $httpClient;
        }
    }

    /**
     * Get RSS feed
     *
     * @param string $url Feed URL
     * @param string $useragent User agent string
     * @return \FeedIo\Reader\Result
     *
     * @throws RequestException if request failed
     */
    public function getFeed(string $url, ?string $useragent = null): Result
    {
        try {
            $client = new \FeedIo\Adapter\Http\Client($this->httpClient);

            if ($useragent !== null) {
                $headers = $this->headers;
                $headers['User-Agent'] = $useragent;

                $client = new \FeedIo\Adapter\Http\Client(
                    new \GuzzleHttp\Client($headers)
                );
            }

            return (new \FeedIo\FeedIo($client))->read($url);
        } catch (\FeedIo\Reader\ReadErrorException $err) {
            $exceptionName = ($err->getPrevious() !== null) ? get_class($err->getPrevious()) : null;

            switch ($exceptionName) {
                case 'FeedIo\Adapter\NotFoundException':
                    $message = sprintf(
                        'Failed to fetch: %s (404 Not Found)',
                        $url,
                    );
                    break;
                case 'FeedIo\Adapter\ServerErrorException':
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

            throw new RequestException($message);
        }
    }

    /**
     * Make a POST request with a JSON payload
     *
     * @param string $url URL
     *
     * @throws RequestException if request failed
     */
    public function postJson(string $url, array $payload, array $headers = []): \Psr\Http\Message\ResponseInterface
    {
        try {
            $options = [
                RequestOptions::JSON => $payload,
                RequestOptions::HEADERS => $headers
            ];

            return $this->httpClient->request(
                'POST',
                $url,
                $options
            );
        } catch (\GuzzleHttp\Exception\ConnectException $err) {
            throw new RequestException($err->getMessage());
        } catch (\GuzzleHttp\Exception\RequestException $err) {
            $response = $err->getResponse();

            if ($response->getHeaderLine('Content-Type') === 'application/json') {
                $this->handleErrorResponseBody(
                    $response->getBody()->getContents(),
                    $response->getStatusCode()
                );
            }

            throw new RequestException(sprintf('Request failed (HTTP %s)', $response->getStatusCode()));
        }
    }

    private function handleErrorResponseBody(string $body, int $statusCode): void
    {
        $json = Json::decode($body);

        if (array_key_exists('errorDescription', $json) === true) {
            throw new RequestException(sprintf(
                '%s: %s (http code: %s)',
                $json['error'],
                $json['errorDescription'],
                $statusCode
            ));
        }

        if (array_key_exists('code', $json) === true) {
            throw new RequestException(sprintf(
                '%s (error code: %s, http code: %s)',
                $json['error'],
                $json['code'],
                $statusCode
            ));
        }
    }
}
