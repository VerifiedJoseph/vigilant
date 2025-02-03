<?php

declare(strict_types=1);

namespace Tests\Feed;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Vigilant\Config;
use Vigilant\Logger;
use Vigilant\Feed\Feed;
use Symfony\Component\Yaml\Yaml;

#[CoversClass(Feed::class)]
#[UsesClass(Config::class)]
#[UsesClass(Logger::class)]
#[UsesClass(\Vigilant\Cache::class)]
#[UsesClass(\Vigilant\Check::class)]
#[UsesClass(\Vigilant\Fetch::class)]
#[UsesClass(\Vigilant\ActiveHours::class)]
#[UsesClass(\Vigilant\Helper\File::class)]
#[UsesClass(\Vigilant\Feed\Details::class)]
#[UsesClass(\Vigilant\Feed\Validate::class)]
#[UsesClass(\Vigilant\Config\Validator::class)]
#[UsesClass(\Vigilant\Config\AbstractValidator::class)]
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
     * Test class
     */
    public function testClass(): void
    {
        $config = self::createStub(Config::class);
        $config->method('getTimezone')->willReturn('UTC');

        $feed = new Feed(self::$feeds[0], $config, new Logger('UTC'));

        $this->assertInstanceOf(
            'Vigilant\Feed\Details',
            $feed->details
        );
    }
}
