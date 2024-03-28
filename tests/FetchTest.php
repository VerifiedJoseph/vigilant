<?php

use Vigilant\Fetch;
use Vigilant\Exception\FetchException;

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
     * Test `get()` with URL that does not return an rss feed
     */
    public function testGetParseError(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to parse feed');

        $fetch = new Fetch();
        $fetch->get('https://example.com');
    }

    /**
     * Test `get()` with URL that returns HTTP 500
     */
    public function testGetServerError(): void
    {
        $this->expectException(FetchException::class);
        $this->expectExceptionMessage('Failed to fetch: https://httpbingo.org/status/500');

        $fetch = new Fetch();
        $fetch->get('https://httpbingo.org/status/500');
    }
}
