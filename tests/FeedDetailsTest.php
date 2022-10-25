<?php

use Vigilant\FeedDetails;
use Symfony\Component\Yaml\Yaml;

class FeedDetailsTest extends TestCase
{
    private static array $feeds = [];
    private static array $details = [];

    public static function setUpBeforeClass(): void
    {
        $feeds = Yaml::parse(self::loadFixture('feeds.yaml'));

        foreach ($feeds['feeds'] as $feed) {
            self::$feeds[] = $feed;
            self::$details[] = new FeedDetails($feed);
        }
    }

    /**
     * Test getHash()
     */
    public function testGetHash(): void
    {
        $hashed = sha1(self::$feeds[0]['url']);

        $this->assertEquals(
            $hashed,
            self::$details[0]->getHash()
        );
    }

    /**
     * Test getName()
     */
    public function testGetName(): void
    {
        $this->assertEquals(
            self::$feeds[0]['name'],
            self::$details[0]->getName()
        );
    }

    /**
     * Test getUrl()
     */
    public function testGetUrl(): void
    {
        $this->assertEquals(
            self::$feeds[0]['url'],
            self::$details[0]->getUrl()
        );
    }

    /**
     * Test getInterval()
     */
    public function testGetInterval(): void
    {
        $this->assertEquals(
            self::$feeds[0]['interval'],
            self::$details[0]->getInterval()
        );
    }

    /**
     * Test getGotifyToken()
     */
    public function testGetGotifyToken(): void
    {
        $this->assertEquals(
            self::$feeds[0]['gotify_token'],
            self::$details[0]->getGotifyToken()
        );
    }

    /**
     * Test getGotifyPriority()
     */
    public function testGetGotifyPriority(): void
    {
        $this->assertEquals(
            self::$feeds[0]['gotify_priority'],
            self::$details[0]->getGotifyPriority()
        );
    }

    /**
     * Test getNtfyTopic()
     */
    public function testGetNtfyTopic(): void
    {
        $this->assertEquals(
            self::$feeds[1]['ntfy_topic'],
            self::$details[1]->getNtfyTopic()
        );
    }

    /**
     * Test getNtfyPriority()
     */
    public function testGetNtfyPriority(): void
    {
        $this->assertEquals(
            self::$feeds[1]['ntfy_priority'],
            self::$details[1]->getNtfyPriority()
        );
    }
}
