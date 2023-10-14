<?php

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
        $feeds = new Feeds(self::getFixturePath('feeds.yaml'));

        $this->assertIsArray($feeds->get());
        $this->assertContainsOnlyInstancesOf('Vigilant\Feed\Details', $feeds->get());
    }

    /**
     * Test Feeds class with an empty file
     */
    public function testNoFeedsException(): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage('No feeds in feeds.yaml');

        new Feeds(self::getFixturePath('feeds-empty-file.yaml'));
    }

    public static function tearDownAfterClass(): void
    {
        Output::disableQuiet();
    }
}
