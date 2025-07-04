<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Vigilant\Feeds;
use Vigilant\Config;
use Vigilant\Logger;
use Vigilant\Exception\AppException;

#[CoversClass(Feeds::class)]
#[UsesClass(Config::class)]
#[UsesClass(Logger::class)]
#[UsesClass(AppException::class)]
#[UsesClass(\Vigilant\Cache::class)]
#[UsesClass(\Vigilant\Check::class)]
#[UsesClass(\Vigilant\Fetch::class)]
#[UsesClass(\Vigilant\Feed\Feed::class)]
#[UsesClass(\Vigilant\Feed\Details::class)]
#[UsesClass(\Vigilant\Feed\Validate::class)]
#[UsesClass(\Vigilant\ActiveHours::class)]
#[UsesClass(\Vigilant\Helper\Time::class)]
#[UsesClass(\Vigilant\Helper\File::class)]
#[UsesClass(\Vigilant\Exception\FeedsException::class)]
class FeedsTest extends TestCase
{
    private static Logger $logger;

    public static function setUpBeforeClass(): void
    {
        self::$logger = new Logger('UTC');
    }

    /**
     * Test get()
     */
    public function testGet(): void
    {
        $config = $this->createStub(Config::class);
        $config->method('getFeedsPath')->willReturn(self::getSamplePath('feeds.yaml'));
        $config->method('getTimezone')->willReturn('UTC');

        $feeds = new Feeds($config, self::$logger);
        $this->assertCount(2, $feeds->get());
    }

    /**
     * Test Feeds class with an empty file
     */
    public function testNoFeedsException(): void
    {
        $config = $this->createStub(Config::class);
        $config->method('getFeedsPath')->willReturn(self::getSamplePath('feeds-empty-file.yaml'));

        $this->expectException(AppException::class);
        $this->expectExceptionMessage('No feeds in feeds.yaml');

        new Feeds($config, self::$logger);
    }

    /**
     * Test Feeds class with non-existent YAML file
     */
    public function testNonExistentYamlFile(): void
    {
        $config = $this->createStub(Config::class);
        $config->method('getFeedsPath')->willReturn(self::getSamplePath('not-exist.yaml'));

        $this->expectException(AppException::class);
        $this->expectExceptionMessageMatches('/Feeds error: File "[\w\-\/]+not-exist\.yaml" does not exist\./');

        new Feeds($config, self::$logger);
    }

    /**
     * Test Feeds class with file containing invalid YAML
     */
    public function testInvalidYamlFile(): void
    {
        $config = $this->createStub(Config::class);
        $config->method('getFeedsPath')->willReturn(self::getSamplePath('invalid-file.yaml'));

        $this->expectException(AppException::class);
        $this->expectExceptionMessage('A colon cannot be used in an unquoted mapping value');

        new Feeds($config, self::$logger);
    }
}
