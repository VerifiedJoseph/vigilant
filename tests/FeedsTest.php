<?php

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Vigilant\Feeds;
use Vigilant\Config;
use Vigilant\Exception\AppException;

#[CoversClass(Feeds::class)]
#[UsesClass(Config::class)]
#[UsesClass(AppException::class)]
#[UsesClass(Vigilant\Output::class)]
#[UsesClass(Vigilant\Feed\Feed::class)]
#[UsesClass(Vigilant\Feed\Details::class)]
#[UsesClass(Vigilant\Feed\Validate::class)]
#[UsesClass(Vigilant\Exception\FeedsException::class)]
class FeedsTest extends TestCase
{
    /**
     * Test get()
     */
    public function testGet(): void
    {
        $this->expectOutputRegex('/Loading feeds.yaml/');

        /** @var PHPUnit\Framework\MockObject\Stub&Config */
        $config = $this->createStub(Config::class);
        $config->method('getFeedsPath')->willReturn(self::getSamplePath('feeds.yaml'));

        $feeds = new Feeds($config);

        $this->assertIsArray($feeds->get());
        $this->assertContainsOnlyInstancesOf('Vigilant\Feed\Details', $feeds->get());
    }

    /**
     * Test Feeds class with an empty file
     */
    public function testNoFeedsException(): void
    {
        /** @var PHPUnit\Framework\MockObject\Stub&Config */
        $config = $this->createStub(Config::class);
        $config->method('getFeedsPath')->willReturn(self::getSamplePath('feeds-empty-file.yaml'));

        $this->expectException(AppException::class);
        $this->expectExceptionMessage('No feeds in feeds.yaml');
        $this->expectOutputRegex('/Loading feeds.yaml/');

        new Feeds($config);
    }

    /**
     * Test Feeds class with file containing invalid YAML
     */
    public function testInvalidYamlFile(): void
    {
        /** @var PHPUnit\Framework\MockObject\Stub&Config */
        $config = $this->createStub(Config::class);
        $config->method('getFeedsPath')->willReturn(self::getSamplePath('invalid-file.yaml'));

        $this->expectException(AppException::class);
        $this->expectExceptionMessage('A colon cannot be used in an unquoted mapping value');
        $this->expectOutputRegex('/Loading feeds.yaml/');

        new Feeds($config);
    }
}
