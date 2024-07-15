<?php

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use Vigilant\Feed\Validate;
use Vigilant\Exception\FeedsException;
use Symfony\Component\Yaml\Yaml;

#[CoversClass(Validate::class)]
#[UsesClass(FeedsException::class)]
#[UsesClass(Vigilant\Helper\Time::class)]
class ValidateTest extends TestCase
{
    /**
     * @var array<string, array<string, mixed>> $feedsInvalid
     */
    private static array $feedsInvalid = [];

    /**
     * @var int $minCheckInterval Minimum feed check interval in seconds
     */
    private static int $minCheckInterval = 300;

    public static function setUpBeforeClass(): void
    {
        $feedsInvalid = Yaml::parse(self::loadSample('feeds-invalid.yaml'));
        self::$feedsInvalid = $feedsInvalid['feeds'];
    }

    /**
     * Test valid feed entry
     */
    #[DoesNotPerformAssertions]
    public function testValidEntry(): void
    {
        $feeds = Yaml::parse(self::loadSample('feeds.yaml'));

        new Validate($feeds['feeds'][0], self::$minCheckInterval);
    }

    /**
     * Test feed entry missing a name value
     */
    public function testNoNameEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('No name given');

        new Validate(self::$feedsInvalid['noName'], self::$minCheckInterval);
    }

    /**
     * Test feed entry with an empty name value
     */
    public function testEmptyNameEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('No name given');

        new Validate(self::$feedsInvalid['emptyName'], self::$minCheckInterval);
    }

    /**
     * Test feed entry missing a URL value
     */
    public function testNoUrlEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('No url given');

        new Validate(self::$feedsInvalid['noUrl'], self::$minCheckInterval);
    }

    /**
     * Test feed entry with an empty URL value
     */
    public function testEmptyUrlEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('No url given');

        new Validate(self::$feedsInvalid['emptyUrl'], self::$minCheckInterval);
    }

    /**
     * Test feed entry missing an interval value
     */
    public function testNoIntervalEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('No interval given');

        new Validate(self::$feedsInvalid['noInterval'], self::$minCheckInterval);
    }

    /**
     * Test feed entry with an empty interval value
     */
    public function testEmptyIntervalEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('No interval given');

        new Validate(self::$feedsInvalid['emptyInterval'], self::$minCheckInterval);
    }

    /**
     * Test feed entry with interval value lower than minimum allowed
     */
    public function testIntervalEntryLowerThanMin(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('Interval is less than 300 seconds for feed');

        new Validate(self::$feedsInvalid['tooLowInterval'], self::$minCheckInterval);
    }

    /**
     * Test feed entry with an empty title prefix value
     */
    public function testEmptyTitlePrefixEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('Empty title prefix given');

        new Validate(self::$feedsInvalid['emptyTitlePrefix'], self::$minCheckInterval);
    }

    /**
     * Test feed entry with an empty gotify token value
     */
    public function testEmptyGotifyTokenEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('Empty Gotify token given');

        new Validate(self::$feedsInvalid['emptyGotifyToken'], self::$minCheckInterval);
    }

    /**
     * Test feed entry with an empty gotify priority value
     */
    public function testEmptyGotifyPriorityEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('Empty Gotify priority given');

        new Validate(self::$feedsInvalid['emptyGotifyPriority'], self::$minCheckInterval);
    }

    /**
     * Test feed entry with an empty ntfy topic value
     */
    public function testEmptyNtfyTopicEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('Empty Ntfy topic given');

        new Validate(self::$feedsInvalid['emptyNtfyTopic'], self::$minCheckInterval);
    }

    /**
     * Test feed entry with an empty ntfy priority value
     */
    public function testEmptyNtfyPriorityEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('Empty Ntfy priority given');

        new Validate(self::$feedsInvalid['emptyNtfyPriority'], self::$minCheckInterval);
    }

    /**
     * Test feed entry with an empty ntfy token value
     */
    public function testEmptyNtfyTokenEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('Empty Ntfy token given');

        new Validate(self::$feedsInvalid['emptyNtfyToken'], self::$minCheckInterval);
    }

    /**
     * Test feed entry with no options for active hours
     */
    public function testNoActiveHoursOptions(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('Required active hours options not given');

        new Validate(self::$feedsInvalid['noActiveHoursOptions'], self::$minCheckInterval);
    }

    /**
     * Test feed entry with no start_time for active hours
     */
    public function testNoActiveHoursStartTime(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('No active hours start time given');

        new Validate(self::$feedsInvalid['noActiveHoursStartTime'], self::$minCheckInterval);
    }

    /**
     * Test feed entry with no end_time for active hours
     */
    public function testNoActiveHoursEndTime(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('No active hours end time given');

        new Validate(self::$feedsInvalid['noActiveHoursEndTime'], self::$minCheckInterval);
    }

    /**
     * Test feed entry with empty start_time for active hours
     */
    public function testEmptyActiveHoursStartTime(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('Empty active hours start time given');

        new Validate(self::$feedsInvalid['emptyActiveHoursStartTime'], self::$minCheckInterval);
    }

    /**
     * Test feed entry with empty end_time for active hours
     */
    public function testEmptyActiveHoursEndTime(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('Empty active hours end time given');

        new Validate(self::$feedsInvalid['emptyActiveHoursEndTime'], self::$minCheckInterval);
    }

    /**
     * Test feed entry with invalid start_time for active hours
     */
    public function testInvalidActiveHoursStartTime(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('Invalid active hours start time given');

        new Validate(self::$feedsInvalid['invalidActiveHoursStartTime'], self::$minCheckInterval);
    }

    /**
     * Test feed entry with invalid end_time for active hours
     */
    public function testInvalidActiveHoursEndTime(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('Invalid active hours end time given');

        new Validate(self::$feedsInvalid['invalidActiveHoursEndTime'], self::$minCheckInterval);
    }
}
