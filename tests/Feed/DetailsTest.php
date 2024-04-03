<?php

use PHPUnit\Framework\Attributes\CoversClass;
use Vigilant\Feed\Details;
use Symfony\Component\Yaml\Yaml;

#[CoversClass(Details::class)]
class DetailsTest extends TestCase
{
    /**
     * @var array<int, array<string, mixed>> $feeds
     */
    private static array $feeds = [];

    /**
     * @var array<int, Details> $details
     */
    private static array $details = [];

    public static function setUpBeforeClass(): void
    {
        $feeds = Yaml::parse(self::loadSample('feeds.yaml'));

        foreach ($feeds['feeds'] as $feed) {
            self::$feeds[] = $feed;
            self::$details[] = new Details($feed);
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
     * Test getNtfyToken()
     */
    public function testGetNtfyToken(): void
    {
        $this->assertEquals(
            self::$feeds[1]['ntfy_token'],
            self::$details[1]->getNtfyToken()
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

    /**
     * Test hasGotifyToken()
     */
    public function testHasGotifyToken(): void
    {
        $this->assertTrue(
            self::$details[0]->hasGotifyToken()
        );
    }

    /**
     * Test hasGotifyPriority()
     */
    public function testHasGotifyPriority(): void
    {
        $this->assertTrue(
            self::$details[0]->hasGotifyPriority()
        );
    }

    /**
     * Test hasNtfyTopic()
     */
    public function testHasNtfyTopic(): void
    {
        $this->assertTrue(
            self::$details[1]->hasNtfyTopic()
        );
    }

    /**
     * Test hasNtfyToken)
     */
    public function testHasNtfyToken(): void
    {
        $this->assertTrue(
            self::$details[1]->hasNtfyToken()
        );
    }

    /**
     * Test hasNtfyPriority()
     */
    public function testHasNtfyPriority(): void
    {
        $this->assertTrue(
            self::$details[1]->hasNtfyPriority()
        );
    }

    /**
     * Test `hasNtfyPriority()` with feed that does not
     */
    public function testDoesNotHaveNtfyPriority(): void
    {
        $this->assertFalse(
            self::$details[0]->hasNtfyPriority()
        );
    }
}
