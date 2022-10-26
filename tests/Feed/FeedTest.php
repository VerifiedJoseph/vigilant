<?php

use Vigilant\Feed\Feed;
use Symfony\Component\Yaml\Yaml;

class FeedTest extends TestCase
{
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
        $feed = new Feed(self::$feeds[0]);

        $this->assertInstanceOf(
            'Vigilant\Feed\Details',
            $feed->getDetails()
        );
    }
}
