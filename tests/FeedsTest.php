<?php

use Vigilant\Feeds;
use Vigilant\Output;

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
}
