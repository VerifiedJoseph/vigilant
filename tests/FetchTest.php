<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Vigilant\Fetch;
use Vigilant\Exception\FetchException;
use GuzzleHttp;
use FeedIo;

#[CoversClass(Fetch::class)]
#[UsesClass(FetchException::class)]
class FetchTest extends TestCase
{
    private string $userAgent = 'Vigilant/Test (https://github.com/VerifiedJoseph/vigilant)';

    /**
     * Test `get()`
     */
    public function testGet(): void
    {
        $fetch = new Fetch($this->userAgent);
        $result = $fetch->get('https://www.githubstatus.com/history.rss');

        $this->assertInstanceOf(FeedIo\Reader\Result::class, $result);
    }

    /**
     * Test `get()` with custom useragent
     */
    public function testGetWithCustomUseragent(): void
    {
        $fetch = new Fetch($this->userAgent);
        $result = $fetch->get(
            'https://www.githubstatus.com/history.rss',
            'Mozilla/5.0 (Windows NT 10.0; rv:146.0) Gecko/20100101 Firefox/146.0'
        );

        $this->assertInstanceOf(FeedIo\Reader\Result::class, $result);
    }

    /**
     * Test `get()` with mock handler that does not return a rss feed
     */
    public function testGetParseError(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to parse feed');

        $mock = new GuzzleHttp\Handler\MockHandler([
            new GuzzleHttp\Psr7\Response(200, body: 'Hello, World'),
        ]);
        $handlerStack = GuzzleHttp\HandlerStack::create($mock);

        $fetch = new Fetch(
            $this->userAgent,
            new \GuzzleHttp\Client(['handler' => $handlerStack])
        );

        $fetch->get('http://example.invalid');
    }

    /**
     * Test `get()` with mock handler that returns HTTP 500
     */
    public function testGetServerError(): void
    {
        $this->expectException(FetchException::class);
        $this->expectExceptionMessage('ailed to fetch: http://example.invalid (500 Internal Server Error)');

        $mock = new GuzzleHttp\Handler\MockHandler([
            new GuzzleHttp\Psr7\Response(500, body: 'server error'),
        ]);
        $handlerStack = GuzzleHttp\HandlerStack::create($mock);

        $fetch = new Fetch(
            $this->userAgent,
            new \GuzzleHttp\Client(['handler' => $handlerStack])
        );

        $fetch->get('http://example.invalid');
    }

    /**
     * Test `get()` with mock handler that returns HTTP 404
     */
    public function testGetNotFoundError(): void
    {
        $this->expectException(FetchException::class);
        $this->expectExceptionMessage('Failed to fetch: http://example.invalid (404 Not Found)');

        $mock = new GuzzleHttp\Handler\MockHandler([
            new GuzzleHttp\Psr7\Response(404, body: 'not found'),
        ]);
        $handlerStack = GuzzleHttp\HandlerStack::create($mock);

        $fetch = new Fetch(
            $this->userAgent,
            new \GuzzleHttp\Client(['handler' => $handlerStack])
        );

        $fetch->get('http://example.invalid');
    }
}
