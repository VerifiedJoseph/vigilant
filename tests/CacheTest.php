<?php

use Vigilant\Cache;
use Vigilant\Helper\Json;

class CacheTest extends TestCase
{
    /**
     * @var string $tempCacheFolder Temp cache folder path
     */
    private static string $tempCacheFolder = '';

    /**
     * @var string $tempCacheFileName Temp cache filename
     */
    private static string $tempCacheFileName = '';

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
        self::$tempCacheFolder = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'vigilant-caching-tests';

        mkdir(self::$tempCacheFolder);
    }

    public function setUp(): void
    {
        self::$tempCacheFileName = bin2hex(random_bytes(5));
        $this->createTempCacheFile();

        self::$fixtureData = Json::decode(self::loadFixture('cache.json'));

        self::$cache = new Cache(
            self::$tempCacheFolder,
            self::$tempCacheFileName
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

        $this->assertIsInt($check);
        $this->assertEquals(
            self::$fixtureData['first_check'],
            $check
        );
    }

    /**
     * Test getIsExpired()
     */
    public function testIsExpired(): void
    {
        $expired = self::$cache->isExpired();

        $this->assertIsBool($expired);
        $this->assertEquals(
            true,
            $expired
        );
    }

    /**
     * Test isFirstCheck()
     */
    public function testIsFirstCheck(): void
    {
        $status = self::$cache->isFirstCheck();

        $this->assertIsBool($status);
        $this->assertEquals(
            false,
            $status
        );
    }

    /**
     * Test getNextCheck()
     */
    public function testGetNextCheck(): void
    {
        $check = self::$cache->getNextCheck();

        $this->assertIsInt($check);
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

        $this->assertIsArray($items);
        $this->assertEquals(
            self::$fixtureData['items'],
            $items
        );
    }

    /**
     * test setFirstCheck
     */
    public function testSetFirstCheck(): void
    {
        $time = time();
        $cache = new Cache(self::$tempCacheFolder, 'testing');

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
        $cache = new Cache(self::$tempCacheFolder, 'testing');
        $cache->save();

        $this->assertFileExists(self::$tempCacheFolder . DIRECTORY_SEPARATOR . 'testing');
        $this->assertJsonFileEqualsJsonFile(
            self::$tempCacheFolder . DIRECTORY_SEPARATOR . 'testing',
            $this->getFixturePath('cache-default.json')
        );

        unlink(self::$tempCacheFolder . DIRECTORY_SEPARATOR . 'testing');
    }

    public function tearDown(): void
    {
        unlink(self::$tempCacheFolder . DIRECTORY_SEPARATOR . self::$tempCacheFileName);
    }

    public static function tearDownAfterClass(): void
    {
        rmdir(self::$tempCacheFolder);
    }

    private function createTempCacheFile(): void
    {
        $file = self::$tempCacheFolder . DIRECTORY_SEPARATOR . self::$tempCacheFileName;

        file_put_contents($file, self::loadFixture('cache.json'));
    }
}
