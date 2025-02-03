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
    /**
     * Test `get()`
     */
    public function testGet(): void
    {
        $fetch = new Fetch();
        $result = $fetch->get('https://www.githubstatus.com/history.rss');

        $this->assertInstanceOf(FeedIo\Reader\Result::class, $result);
    }

    /**
     * Test `get()` with mock handler that does not return a rss feed
     */
    public function testGetParseError(): void
    {
        $mock = new GuzzleHttp\Handler\MockHandler([
            new GuzzleHttp\Psr7\Response(200, body: 'Hello, World'),
        ]);
        $handlerStack = GuzzleHttp\HandlerStack::create($mock);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to parse feed');

        $fetch = new Fetch(new \GuzzleHttp\Client(['handler' => $handlerStack]));
        $fetch->get('http://example.invalid');
    }

    /**
     * Test `get()` with mock handler that returns HTTP 500
     */
    public function testGetServerError(): void
    {
        $mock = new GuzzleHttp\Handler\MockHandler([
            new GuzzleHttp\Psr7\Response(500, body: 'server error'),
        ]);
        $handlerStack = GuzzleHttp\HandlerStack::create($mock);

        $this->expectException(FetchException::class);
        $this->expectExceptionMessage('ailed to fetch: http://example.invalid (500 Internal Server Error)');

        $fetch = new Fetch(new \GuzzleHttp\Client(['handler' => $handlerStack]));
        $fetch->get('http://example.invalid');
    }

    /**
     * Test `get()` with mock handler that returns HTTP 404
     */
    public function testGetNotFoundError(): void
    {
        $mock = new GuzzleHttp\Handler\MockHandler([
            new GuzzleHttp\Psr7\Response(404, body: 'not found'),
        ]);
        $handlerStack = GuzzleHttp\HandlerStack::create($mock);

        $this->expectException(FetchException::class);
        $this->expectExceptionMessage('Failed to fetch: http://example.invalid (404 Not Found)');

        $fetch = new Fetch(new \GuzzleHttp\Client(['handler' => $handlerStack]));
        $fetch->get('http://example.invalid');
    }
}
