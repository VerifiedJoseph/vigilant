<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use MockFileSystem\MockFileSystem as mockfs;
use Vigilant\Cache;
use Vigilant\Config;
use Vigilant\Helper\Json;

#[CoversClass(Cache::class)]
#[UsesClass(Config::class)]
#[UsesClass(Json::class)]
#[UsesClass(Vigilant\Helper\File::class)]
class CacheTest extends TestCase
{
    /**
     * @var string $tempCacheFolder Temp cache folder path
     */
    private static string $tempCacheFolder = '';

    /**
     * @var array<string, mixed> fixtureData Data from fixture cache.json
     */
    private static array $fixtureData = [];

    /**
     * @var Cache $cache
     */
    private static Cache $cache;

    public static function setUpBeforeClass(): void
    {
        mockfs::create();
        mkdir(mockfs::getUrl('/cache'));

        self::$tempCacheFolder = mockfs::getUrl('/cache');
    }

    public function setUp(): void
    {
        mockfs::create();
        mkdir(mockfs::getUrl('/cache'));
        file_put_contents(mockfs::getUrl('/cache/file'), self::loadSample('cache.json'));

        self::$fixtureData = Json::decode(self::loadSample('cache.json'));

        $config = self::createStub(Config::class);
        $config->method('getCachePath')->willReturn(mockfs::getUrl('/cache'));
        $config->method('getCacheFormatVersion')->willReturn(1);

        self::$cache = new Cache(
            'file',
            $config
        );
    }

    /**
     * Test getFeedUrl()
     */
    public function testGetFeedUrl(): void
    {
        $url = self::$cache->getFeedUrl();

        $this->assertIsString($url);
        $this->assertEquals(
            self::$fixtureData['feed_url'],
            $url
        );
    }

    /**
     * Test getFirstCheck()
     */
    public function testGetFirstCheck(): void
    {
        $check = self::$cache->getFirstCheck();

        $this->assertEquals(
            self::$fixtureData['first_check'],
            $check
        );
    }

    /**
     * Test isFirstCheck() returns true
     */
    public function testIsFirstCheck(): void
    {
        $config = self::createStub(Config::class);
        $config->method('getCachePath')->willReturn(self::$tempCacheFolder);
        $config->method('getCacheFormatVersion')->willReturn(1);

        $cache = new Cache('testing', $config);
        $this->assertTrue($cache->isFirstCheck());
    }

    /**
     * Test isFirstCheck() returns value false
     */
    public function testIsFirstCheckFalse(): void
    {
        $this->assertFalse(self::$cache->isFirstCheck());
    }

    /**
     * Test getIsExpired()
     */
    public function testIsExpired(): void
    {
        $this->assertTrue(self::$cache->isExpired());
    }

    /**
     * Test getIsExpired() returns value false
     */
    public function testIsExpiredFalse(): void
    {
        self::$cache->updateNextCheck(300);
        $this->assertFalse(self::$cache->isExpired());
    }

    /**
     * Test getNextCheck()
     */
    public function testGetNextCheck(): void
    {
        $check = self::$cache->getNextCheck();

        $this->assertEquals(
            self::$fixtureData['next_check'],
            $check
        );
    }

    /**
     * Test getItems()
     */
    public function testGetItems(): void
    {
        $items = self::$cache->getItems();

        $this->assertEquals(
            self::$fixtureData['items'],
            $items
        );
    }

    /**
     * Test getErrorCount()
     */
    public function testGetErrorCount(): void
    {
        $count = self::$cache->getErrorCount();

        $this->assertEquals(
            self::$fixtureData['error_count'],
            $count
        );
    }

    /**
     * Test increaseErrorCount()
     */
    public function testIncreaseErrorCount(): void
    {
        self::$cache->increaseErrorCount();
        $count = self::$cache->getErrorCount();

        $this->assertEquals(
            2,
            $count
        );
    }

    /**
     * Test resetErrorCount()
     */
    public function testResetErrorCount(): void
    {
        self::$cache->resetErrorCount();
        $count = self::$cache->getErrorCount();

        $this->assertEquals(
            0,
            $count
        );
    }

    /**
     * test setFirstCheck
     */
    public function testSetFirstCheck(): void
    {
        $config = self::createStub(Config::class);
        $config->method('getCachePath')->willReturn(self::$tempCacheFolder);
        $config->method('getCacheFormatVersion')->willReturn(1);

        $time = time();
        $cache = new Cache('testing', $config);

        $this->assertEquals(0, $cache->getFirstCheck());

        $cache->setFirstCheck();

        $this->assertGreaterThanOrEqual($time, $cache->getFirstCheck());
    }

    /**
     * test setFirstCheck()
     */
    public function testSetFeedUrl(): void
    {
        $url = 'https://www.example.org/feed.rss';
        self::$cache->setFeedUrl($url);

        $this->assertEquals(
            $url,
            self::$cache->getFeedUrl()
        );
    }

    /**
     * test updateNextCheck()
     */
    public function testUpdateNextCheck(): void
    {
        $time = time() + 300;
        self::$cache->updateNextCheck(300);

        $this->assertGreaterThanOrEqual(
            $time,
            self::$cache->getNextCheck()
        );
    }

    /**
     * Test UpdateItems()
     */
    public function testUpdateItems(): void
    {
        $items = ['iUz0s3QaHS', 'QwZF6yVJVu'];
        self::$cache->updateItems($items);

        $this->assertEquals(
            $items,
            self::$cache->getItems()
        );
    }

    /**
     * Test save()
     */
    public function testSave(): void
    {
        $config = self::createStub(Config::class);
        $config->method('getCachePath')->willReturn(self::$tempCacheFolder);
        $config->method('getCacheFormatVersion')->willReturn(1);

        $cache = new Cache('testing', $config);
        $cache->save();

        $this->assertFileExists(mockfs::getUrl('/cache/testing'));
        $this->assertJsonFileEqualsJsonFile(
            mockfs::getUrl('/cache/testing'),
            $this->getSamplePath('cache-default.json')
        );
    }


    /**
     * Test cache version that does not match current cache version
     */
    public function testNoVersionMatch(): void
    {
        $config = self::createStub(Config::class);
        $config->method('getCachePath')->willReturn(mockfs::getUrl('/cache'));
        $config->method('getCacheFormatVersion')->willReturn(2);

        $cache = new Cache('file', $config);

        $this->assertEquals([], $cache->getItems());
    }
}
