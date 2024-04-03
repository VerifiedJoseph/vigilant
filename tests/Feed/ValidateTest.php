<?php

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Vigilant\Feed\Validate;
use Vigilant\Exception\FeedsException;
use Symfony\Component\Yaml\Yaml;

#[CoversClass(Validate::class)]
#[UsesClass(FeedsException::class)]
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
     * Test validator with valid feed entry
     */
    public function testValidateWithValidEntry(): void
    {
        $this->expectNotToPerformAssertions();

        $feeds = Yaml::parse(self::loadSample('feeds.yaml'));

        new Validate($feeds['feeds'][0], self::$minCheckInterval);
    }

    /**
     * Test validator with feed entry missing a name value
     */
    public function testValidateWithNoNameEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('No name given');

        new Validate(self::$feedsInvalid['noName'], self::$minCheckInterval);
    }

    /**
     * Test validator with feed entry with an empty name value
     */
    public function testValidateWithEmptyNameEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('No name given');

        new Validate(self::$feedsInvalid['emptyName'], self::$minCheckInterval);
    }

    /**
     * Test validator with feed entry missing a URL value
     */
    public function testValidateWithNoUrlEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('No url given');

        new Validate(self::$feedsInvalid['noUrl'], self::$minCheckInterval);
    }

    /**
     * Test validator with feed entry with an empty URL value
     */
    public function testValidateWithEmptyUrlEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('No url given');

        new Validate(self::$feedsInvalid['emptyUrl'], self::$minCheckInterval);
    }

    /**
     * Test validator with feed entry missing an interval value
     */
    public function testValidateWithNoIntervalEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('No interval given');

        new Validate(self::$feedsInvalid['noInterval'], self::$minCheckInterval);
    }

    /**
     * Test validator with feed entry with an empty interval value
     */
    public function testValidateWithEmptyIntervalEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('No interval given');

        new Validate(self::$feedsInvalid['emptyInterval'], self::$minCheckInterval);
    }

    /**
     * Test validator with feed entry with an interval value lower than minimum allowed
     */
    public function testValidateWithIntervalEntryLowerThanMin(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('Interval is less than 300 seconds for feed');

        new Validate(self::$feedsInvalid['tooLowInterval'], self::$minCheckInterval);
    }

    /**
     * Test validator with feed entry with an empty gotify token value
     */
    public function testValidateWithEmptyGotifyTokenEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('Empty Gotify token given');

        new Validate(self::$feedsInvalid['emptyGotifyToken'], self::$minCheckInterval);
    }

    /**
     * Test validator with feed entry with an empty gotify priority value
     */
    public function testValidateWithEmptyGotifyPriorityEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('Empty Gotify priority given');

        new Validate(self::$feedsInvalid['emptyGotifyPriority'], self::$minCheckInterval);
    }

    /**
     * Test validator with feed entry with an empty ntfy topic value
     */
    public function testValidateWithEmptyNtfyTopicEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('Empty Ntfy topic given');

        new Validate(self::$feedsInvalid['emptyNtfyTopic'], self::$minCheckInterval);
    }

    /**
     * Test validator with feed entry with an empty ntfy priority value
     */
    public function testValidateWithEmptyNtfyPriorityEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('Empty Ntfy priority given');

        new Validate(self::$feedsInvalid['emptyNtfyPriority'], self::$minCheckInterval);
    }

    /**
     * Test validator with feed entry with an empty ntfy token value
     */
    public function testValidateWithEmptyNtfyTokenEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('Empty Ntfy token given');

        new Validate(self::$feedsInvalid['emptyNtfyToken'], self::$minCheckInterval);
    }
}
