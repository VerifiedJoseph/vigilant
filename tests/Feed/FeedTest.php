<?php

use Vigilant\Config;
use Vigilant\Feed\Feed;
use Symfony\Component\Yaml\Yaml;

class FeedTest extends TestCase
{
    /**
     * @var array<int, array<string, mixed>> $feeds
     */
    private static array $feeds = [];

    public static function setUpBeforeClass(): void
    {
        $feeds = Yaml::parse(self::loadFixture('feeds.yaml'));
        self::$feeds = $feeds['feeds'];
    }

    /**
     * Test get()
     */
    public function testGet(): void
    {
        $config = new Config();

        $feed = new Feed(self::$feeds[0], $config);

        $this->assertInstanceOf(
            'Vigilant\Feed\Details',
            $feed->getDetails()
        );
    }
}
