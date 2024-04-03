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
        $details = new Details(self::$feeds[0]);

        $this->assertEquals(
            $hashed,
            $details->getHash()
        );
    }

    /**
     * Test getName()
     */
    public function testGetName(): void
    {
        $details = new Details(self::$feeds[0]);

        $this->assertEquals(
            self::$feeds[0]['name'],
            $details->getName()
        );
    }

    /**
     * Test getUrl()
     */
    public function testGetUrl(): void
    {
        $details = new Details(self::$feeds[0]);

        $this->assertEquals(
            self::$feeds[0]['url'],
            $details->getUrl()
        );
    }

    /**
     * Test getInterval()
     */
    public function testGetInterval(): void
    {
        $details = new Details(self::$feeds[0]);

        $this->assertEquals(
            self::$feeds[0]['interval'],
            $details->getInterval()
        );
    }

    /**
     * Test getGotifyToken()
     */
    public function testGetGotifyToken(): void
    {
        $details = new Details(self::$feeds[0]);

        $this->assertEquals(
            self::$feeds[0]['gotify_token'],
            $details->getGotifyToken()
        );
    }

    /**
     * Test getGotifyPriority()
     */
    public function testGetGotifyPriority(): void
    {
        $details = new Details(self::$feeds[0]);

        $this->assertEquals(
            self::$feeds[0]['gotify_priority'],
            $details->getGotifyPriority()
        );
    }

    /**
     * Test getNtfyTopic()
     */
    public function testGetNtfyTopic(): void
    {
        $details = new Details(self::$feeds[1]);

        $this->assertEquals(
            self::$feeds[1]['ntfy_topic'],
            $details->getNtfyTopic()
        );
    }

    /**
     * Test getNtfyToken()
     */
    public function testGetNtfyToken(): void
    {
        $details = new Details(self::$feeds[1]);

        $this->assertEquals(
            self::$feeds[1]['ntfy_token'],
            $details->getNtfyToken()
        );
    }

    /**
     * Test getNtfyPriority()
     */
    public function testGetNtfyPriority(): void
    {
        $details = new Details(self::$feeds[1]);

        $this->assertEquals(
            self::$feeds[1]['ntfy_priority'],
            $details->getNtfyPriority()
        );
    }

    /**
     * Test hasGotifyToken()
     */
    public function testHasGotifyToken(): void
    {
        $details = new Details(self::$feeds[0]);
        $this->assertTrue($details->hasGotifyToken());
    }

    /**
     * Test hasGotifyPriority()
     */
    public function testHasGotifyPriority(): void
    {
        $details = new Details(self::$feeds[0]);
        $this->assertTrue($details->hasGotifyPriority());
    }

    /**
     * Test hasNtfyTopic()
     */
    public function testHasNtfyTopic(): void
    {
        $details = new Details(self::$feeds[1]);
        $this->assertTrue($details->hasNtfyTopic());
    }

    /**
     * Test hasNtfyToken)
     */
    public function testHasNtfyToken(): void
    {
        $details = new Details(self::$feeds[1]);
        $this->assertTrue($details->hasNtfyToken());
    }

    /**
     * Test hasNtfyPriority()
     */
    public function testHasNtfyPriority(): void
    {
        $details = new Details(self::$feeds[1]);
        $this->assertTrue($details->hasNtfyPriority());
    }

    /**
     * Test `hasNtfyPriority()` returning false
     */
    public function testHasNtfyPriorityNtfyPriorityFalse(): void
    {
        $details = new Details(self::$feeds[0]);
        $this->assertFalse($details->hasNtfyPriority());
    }
}
