<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Vigilant\Request;
use Vigilant\Exception\RequestException;
use GuzzleHttp;
use FeedIo;

#[CoversClass(Request::class)]
#[UsesClass(RequestException::class)]
#[UsesClass(\Vigilant\Helper\Json::class)]
class RequestTest extends TestCase
{
    private string $userAgent = 'Vigilant/Test (https://github.com/VerifiedJoseph/vigilant)';

    /**
     * Test `getFeed()`
     */
    public function testGetFeed(): void
    {
        $request = new Request($this->userAgent);
        $result = $request->getFeed('https://www.githubstatus.com/history.rss');

        $this->assertInstanceOf(FeedIo\Reader\Result::class, $result);
    }

    /**
     * Test `getFeed()` with custom useragent
     */
    public function testGetFeedWithCustomUseragent(): void
    {
        $request = new Request($this->userAgent);
        $result = $request->getFeed(
            'https://www.githubstatus.com/history.rss',
            'Mozilla/5.0 (Windows NT 10.0; rv:146.0) Gecko/20100101 Firefox/146.0'
        );

        $this->assertInstanceOf(FeedIo\Reader\Result::class, $result);
    }

    /**
     * Test `getFeed()` with mock handler that does not return a rss feed
     */
    public function testGetFeedParseError(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to parse feed');

        $mock = new GuzzleHttp\Handler\MockHandler([
            new GuzzleHttp\Psr7\Response(200, body: 'Hello, World'),
        ]);
        $handlerStack = GuzzleHttp\HandlerStack::create($mock);

        $request = new Request(
            $this->userAgent,
            new \GuzzleHttp\Client(['handler' => $handlerStack])
        );

        $request->getFeed('http://example.invalid');
    }

    /**
     * Test `getFeed()` with mock handler that returns HTTP 500
     */
    public function testGetFeedServerError(): void
    {
        $this->expectException(RequestException::class);
        $this->expectExceptionMessage('Failed to fetch: http://example.invalid (500 Internal Server Error)');

        $mock = new GuzzleHttp\Handler\MockHandler([
            new GuzzleHttp\Psr7\Response(500, body: 'server error'),
        ]);
        $handlerStack = GuzzleHttp\HandlerStack::create($mock);

        $request = new Request(
            $this->userAgent,
            new \GuzzleHttp\Client(['handler' => $handlerStack])
        );

        $request->getFeed('http://example.invalid');
    }

    /**
     * Test `getFeed()` with mock handler that returns HTTP 404
     */
    public function testGetFeedNotFoundError(): void
    {
        $this->expectException(RequestException::class);
        $this->expectExceptionMessage('Failed to fetch: http://example.invalid (404 Not Found)');

        $mock = new GuzzleHttp\Handler\MockHandler([
            new GuzzleHttp\Psr7\Response(404, body: 'not found'),
        ]);
        $handlerStack = GuzzleHttp\HandlerStack::create($mock);

        $request = new Request(
            $this->userAgent,
            new \GuzzleHttp\Client(['handler' => $handlerStack])
        );

        $request->getFeed('http://example.invalid');
    }

    /**
     * Test `postJson()`
     */
    public function testPostJson(): void
    {
        $payload = ['hello' => 'world'];
        $headers = ['Authorization' => 'Bearer testing'];

        $request = new Request($this->userAgent);
        $response = $request->postJson(
            'https://httpbingo.org/anything',
            $payload,
            $headers
        );

        $data = json_decode($response->getBody()->getContents(), associative: true);

        $this->assertArrayHasKey('Authorization', $data['headers']);
        $this->assertArrayHasKey('User-Agent', $data['headers']);

        $this->assertEquals($this->userAgent, $data['headers']['User-Agent'][0]);
        $this->assertEquals('Bearer testing', $data['headers']['Authorization'][0]);
        $this->assertEquals($payload, $data['json']);
    }

    /**
     * Test `postJson()` with mock handler that returns connection failure
     */
    public function testPostJsonWithConnectionFailure(): void
    {
        $this->expectException(RequestException::class);

        $payload = ['hello' => 'world'];

        $request = new Request($this->userAgent);
        $request->postJson(
            'http://example.invalid',
            $payload,
        );
    }

    /**
     * Test `postJson()` with mock handler that returns a generic server 500 error
     */
    public function testPostJsonWithGenericError(): void
    {
        $this->expectException(RequestException::class);
        $this->expectExceptionMessage('Request failed (HTTP 500)');

        $mock = new GuzzleHttp\Handler\MockHandler([
            new GuzzleHttp\Psr7\Response(
                500,
                body: 'fg'
            ),
        ]);
        $handlerStack = GuzzleHttp\HandlerStack::create($mock);

        $request = new Request(
            $this->userAgent,
            new \GuzzleHttp\Client(['handler' => $handlerStack])
        );
        $request->postJson('http://example.com', []);
    }

    /**
     * Test `postJson()` with mock handler that returns a Gotify server error
     */
    public function testPostJsonWithGotifyError(): void
    {
        $this->expectException(RequestException::class);
        $this->expectExceptionMessage(
            'Unauthorized: you need to provide a valid token to access this api'
        );

        $responseBody = [
            'error' => 'Unauthorized',
            'errorCode' => 401,
            'errorDescription' => 'you need to provide a valid token to access this api'
        ];

        $mock = new GuzzleHttp\Handler\MockHandler([
            new GuzzleHttp\Psr7\Response(
                401,
                ['Content-Type' => 'application/json'],
                json_encode($responseBody)
            ),
        ]);
        $handlerStack = GuzzleHttp\HandlerStack::create($mock);

        $request = new Request(
            $this->userAgent,
            new \GuzzleHttp\Client(['handler' => $handlerStack])
        );
        $request->postJson('http://example.invalid', []);
    }

    /**
     * Test `postJson()`  with mock handler that returns a Ntfy server error
     */
    public function testPostJsonWithNtfyError(): void
    {
        $this->expectException(RequestException::class);
        $this->expectExceptionMessage('forbidden (error code: 40301, http code: 403)');

        $mock = new GuzzleHttp\Handler\MockHandler([
            new GuzzleHttp\Psr7\Response(
                403,
                ['Content-Type' => 'application/json'],
                json_encode([
                    'error' => 'forbidden',
                    'code' => 40301,
                ])
            ),
        ]);
        $handlerStack = GuzzleHttp\HandlerStack::create($mock);

        $request = new Request(
            $this->userAgent,
            new \GuzzleHttp\Client(['handler' => $handlerStack])
        );
        $request->postJson('http://example.invalid', []);
    }
}
