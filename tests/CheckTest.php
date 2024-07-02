<?php

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use MockFileSystem\MockFileSystem as mockfs;
use Vigilant\Check;
use Vigilant\Feed\Details;
use Vigilant\Config;
use Vigilant\Logger;
use Vigilant\Fetch;

#[CoversClass(Check::class)]
#[UsesClass(Details::class)]
#[UsesClass(Fetch::class)]
#[UsesClass(Config::class)]
#[UsesClass(Logger::class)]
#[UsesClass(Vigilant\Cache::class)]
#[UsesClass(Vigilant\Output::class)]
#[UsesClass(Vigilant\Message::class)]
#[UsesClass(Vigilant\Helper\File::class)]
#[UsesClass(Vigilant\Helper\Json::class)]
class CheckTest extends TestCase
{
    private static Config $config;
    private static Logger $logger;

    /** @var array<string, mixed> $feed */
    private array $feed = [
        'name' => 'Example',
        'url' => 'https://www.example.com/feed.rss',
        'interval' => 300
    ];

    /** @var array<string, mixed> $cache */
    private array $cache = [
        'feed_url' => 'https://www.example.com/feed.rss',
        'first_check' => 1666292400,
        'next_check' => 0,
        'error_count' => 3,
        'items' => ['4a12f90c6959a0b0cc134ea2a0564ade5d779c50'],
        'version' => 1
    ];

    public static function setUpBeforeClass(): void
    {
        mockfs::create();

        /** @var PHPUnit\Framework\MockObject\Stub&Config */
        $config = self::createStub(Config::class);
        $config->method('getCachePath')->willReturn(mockfs::getUrl('/'));
        $config->method('getCacheFormatVersion')->willReturn(1);
        $config->method('getTimezone')->willReturn('UTC');
        self::$config = $config;

        self::$logger = new Logger('UTC');
    }

    public function setup(): void
    {
        mockfs::create();
    }

    /**
     * Test `isDue()`
     */
    public function testIsDue(): void
    {
        $details = new Details($this->feed);
        $check = new check($details, new Fetch(), self::$config, self::$logger);
        $this->assertIsBool($check->isDue());
    }

    /**
     * Test `getNextCheckDate()`
     */
    public function testGetNextCheckDate(): void
    {
        $details = new Details($this->feed);
        $check = new check($details, new Fetch(), self::$config, self::$logger);

        $this->assertEquals('1970-01-01 00:00:00', $check->getNextCheckDate());
    }

    /**
     * test `check()` with a first time check
     */
    public function testCheckFirstTime(): void
    {
        $mock = new GuzzleHttp\Handler\MockHandler([
            new GuzzleHttp\Psr7\Response(200), // First response is needed as feedio makes HEAD request.
            new GuzzleHttp\Psr7\Response(200, body: (string) file_get_contents('tests/files/rss-feed.xml'))
        ]);
        $fetch = new Fetch(new \GuzzleHttp\Client([
            'handler' => GuzzleHttp\HandlerStack::create($mock)
        ]));

        $details = new Details($this->feed);
        $check = new check($details, $fetch, self::$config, self::$logger);

        $this->expectOutputRegex('/First feed check, not sending notifications for found items/');

        $check->check();
        $this->assertCount(0, $check->getMessages());
        $this->assertNotEquals('1970-01-01 00:00:00', $check->getNextCheckDate());
    }

    /**
     * Test `check()` with a cache file for feed
     */
    public function testCheckWithCache(): void
    {
        $this->createCacheFIle(sha1($this->feed['url']), $this->cache);

        $mock = new GuzzleHttp\Handler\MockHandler([
            new GuzzleHttp\Psr7\Response(200), // First response is needed as feedio makes HEAD request.
            new GuzzleHttp\Psr7\Response(200, body: (string) file_get_contents('tests/files/rss-feed.xml'))
        ]);
        $fetch = new Fetch(new \GuzzleHttp\Client([
            'handler' => GuzzleHttp\HandlerStack::create($mock)
        ]));

        $details = new Details($this->feed);
        $check = new check($details, $fetch, self::$config, self::$logger);

        $this->expectOutputRegex('/Found 1 new item\(s\)/');

        $check->check();
        $messages = $check->getMessages();

        $this->assertCount(1, $messages);
        $this->assertEquals('Item 2', $messages[0]->getTitle());
        $this->assertEquals('This is item 2', $messages[0]->getBody());
    }

    /**
     * Test `check()` when `Fetch` class returns an error and
     * cache `error_count` value is one below the max to trigger a message
     * telling the user that fetching the feed has repeatedly failed.
     */
    public function testCheckFetchErrorMessage(): void
    {
        $cache = $this->cache;
        $cache['error_count'] = 3;
        $this->createCacheFIle(sha1($this->feed['url']), $cache);

        $mock = new GuzzleHttp\Handler\MockHandler([
            new GuzzleHttp\Psr7\Response(404),
        ]);
        $fetch = new Fetch(new \GuzzleHttp\Client([
            'handler' => GuzzleHttp\HandlerStack::create($mock)
        ]));
        $details = new Details($this->feed);
        $check = new check($details, $fetch, self::$config, self::$logger);

        $this->expectOutputRegex('/Failed to fetch/');

        $check->check();
        $messages = $check->getMessages();

        $this->assertCount(1, $messages);
        $this->assertEquals('[Vigilant] Error when fetching Example', $messages[0]->getTitle());
        $this->assertEquals(
            'Failed to fetch: https://www.example.com/feed.rss (404 Not Found)',
            $messages[0]->getBody()
        );
    }

    /**
     * Create cache file in mock file system
     * @param string $filename
     * @param array<string, mixed> $data
     */
    private function createCacheFIle(string $filename, array $data): void
    {
        file_put_contents(mockfs::getUrl('/' . $filename), json_encode($data));
    }
}
