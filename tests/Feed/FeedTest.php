<?php

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Vigilant\Config;
use Vigilant\Feed\Feed;
use Symfony\Component\Yaml\Yaml;

#[CoversClass(Feed::class)]
#[UsesClass(Config::class)]
#[UsesClass(Vigilant\Config\Validate::class)]
#[UsesClass(Vigilant\Feed\Details::class)]
#[UsesClass(Vigilant\Feed\Validate::class)]
class FeedTest extends TestCase
{
    /**
     * @var array<int, array<string, mixed>> $feeds
     */
    private static array $feeds = [];

    public static function setUpBeforeClass(): void
    {
        $feeds = Yaml::parse(self::loadSample('feeds.yaml'));
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
