<?php

use Vigilant\Config;
use Vigilant\Feeds;
use Vigilant\Output;
use Vigilant\Exception\AppException;

class FeedsTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        Output::quiet();
    }

    /**
     * Test get()
     */
    public function testGet(): void
    {
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

        new Feeds($config);
    }

    public static function tearDownAfterClass(): void
    {
        Output::disableQuiet();
    }
}
