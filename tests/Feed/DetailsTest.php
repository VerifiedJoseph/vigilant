<?php

declare(strict_types=1);

namespace Tests\Feed;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Vigilant\Feed\Details;
use Symfony\Component\Yaml\Yaml;
use ReflectionClass;

#[CoversClass(Details::class)]
class DetailsTest extends TestCase
{
    /**
     * @var array<int, array<string, mixed>> $feeds
     */
    private static array $feeds = [];

    /**
     * @var array<int, Details>
     */
    private static array $details = [];

    public static function setUpBeforeClass(): void
    {
        $feeds = Yaml::parse(self::loadSample('feeds.yaml'));
        self::$feeds = $feeds['feeds'];

        self::$details = [
            new Details(self::$feeds[0]),
            new Details(self::$feeds[1])
        ];
    }

    /**
     * Test __construct
     */
    public function testConstruct(): void
    {
        $details = new Details(self::$feeds[0]);

        $reflection = new ReflectionClass($details);
        $property = $reflection->getProperty('details');

        $this->assertEquals(
            self::$feeds[0],
            $property->getValue($details)
        );
    }

    /**
     * Test getHash()
     */
    public function testGetHash(): void
    {
        $this->assertEquals(
            sha1(self::$feeds[0]['url']),
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
     * Test getTitlePrefix()
     */
    public function testGetTitlePrefix(): void
    {
        $this->assertEquals(
            self::$feeds[0]['title_prefix'],
            self::$details[0]->getTitlePrefix()
        );

        $this->assertNull(
            self::$details[1]->getTitlePrefix()
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

        $this->assertNull(
            self::$details[1]->getGotifyToken()
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

        $this->assertNull(
            self::$details[1]->getGotifyPriority()
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

        $this->assertNull(
            self::$details[0]->getNtfyTopic()
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

        $this->assertNull(
            self::$details[0]->getNtfyToken()
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

        $this->assertNull(
            self::$details[0]->getNtfyPriority()
        );
    }

    public function testGetTruncateStatus(): void
    {
        $this->assertEquals(
            self::$feeds[1]['truncate'],
            self::$details[1]->getTruncateStatus()
        );

        $this->assertFalse(
            self::$details[0]->getTruncateStatus()
        );
    }

    public function testGetTruncateLength(): void
    {
        $this->assertEquals(
            self::$feeds[1]['truncate_length'],
            self::$details[1]->getTruncateLength()
        );

        $this->assertNull(
            self::$details[0]->getTruncateLength()
        );
    }

    /**
     * Test getActiveHoursStartTime()
     */
    public function testGetActiveHoursStartTime(): void
    {
        $this->assertEquals(
            self::$feeds[1]['active_hours']['start_time'],
            self::$details[1]->getActiveHoursStartTime()
        );

        $this->assertNull(
            self::$details[0]->getActiveHoursStartTime()
        );
    }

    /**
     * Test getActiveHoursEndTime()
     */
    public function testGetActiveHoursEndTime(): void
    {
        $this->assertEquals(
            self::$feeds[1]['active_hours']['end_time'],
            self::$details[1]->getActiveHoursEndTime()
        );

        $this->assertNull(
            self::$details[0]->getActiveHoursEndTime()
        );
    }

    /**
     * Test hasActiveHours()
     */
    public function testHasActiveHours(): void
    {
        $this->assertTrue(self::$details[1]->hasActiveHours());
        $this->assertFalse(self::$details[0]->hasActiveHours());
    }

    /**
     * Test hasGotifyToken()
     */
    public function testHasGotifyToken(): void
    {
        $this->assertTrue(self::$details[0]->hasGotifyToken());
        $this->assertFalse(self::$details[1]->hasGotifyToken());
    }

    /**
     * Test hasGotifyPriority()
     */
    public function testHasGotifyPriority(): void
    {
        $this->assertTrue(self::$details[0]->hasGotifyPriority());
        $this->assertFalse(self::$details[1]->hasGotifyPriority());
    }

    /**
     * Test hasNtfyTopic()
     */
    public function testHasNtfyTopic(): void
    {
        $this->assertTrue(self::$details[1]->hasNtfyTopic());
        $this->assertFalse(self::$details[0]->hasNtfyTopic());
    }

    /**
     * Test hasNtfyToken()
     */
    public function testHasNtfyToken(): void
    {
        $this->assertTrue(self::$details[1]->hasNtfyToken());
        $this->assertFalse(self::$details[0]->hasNtfyToken());
    }

    /**
     * Test hasNtfyPriority()
     */
    public function testHasNtfyPriority(): void
    {
        $this->assertTrue(self::$details[1]->hasNtfyPriority());
        $this->assertFalse(self::$details[0]->hasNtfyPriority());
    }
}
