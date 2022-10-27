<?php

use Vigilant\Feed\Validate;
use Vigilant\Exception\FeedsException;

use Symfony\Component\Yaml\Yaml;

class ValidateTest extends TestCase
{
    /**
     * @var array<string, array<string, mixed>> $feedsInvalid
     */
    private static array $feedsInvalid = [];

    public static function setUpBeforeClass(): void
    {
        $feedsInvalid = Yaml::parse(self::loadFixture('feeds-invalid.yaml'));
        self::$feedsInvalid = $feedsInvalid['feeds'];
    }

    /**
     * Test validator with valid feed entry
     */
    public function testValidateWithValidEntry(): void
    {
        $feeds = Yaml::parse(self::loadFixture('feeds.yaml'));

        new Validate($feeds['feeds'][0]);

        $this->assertTrue(true);
    }

    /**
     * Test validator with feed entry missing a name value
     */
    public function testValidateWithNoNameEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('No name given');

        new Validate(self::$feedsInvalid['noName']);
    }

    /**
     * Test validator with feed entry with an empty name value
     */
    public function testValidateWithEmptyNameEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('No name given');

        new Validate(self::$feedsInvalid['emptyName']);
    }

    /**
     * Test validator with feed entry missing a URL value
     */
    public function testValidateWithNoUrlEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('No url given');

        new Validate(self::$feedsInvalid['noUrl']);
    }

    /**
     * Test validator with feed entry with an empty URL value
     */
    public function testValidateWithEmptyUrlEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('No url given');

        new Validate(self::$feedsInvalid['emptyUrl']);
    }

    /**
     * Test validator with feed entry missing an interval value
     */
    public function testValidateWithNoIntervalEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('No interval given');

        new Validate(self::$feedsInvalid['noInterval']);
    }

    /**
     * Test validator with feed entry with an empty interval value
     */
    public function testValidateWithEmptyIntervalEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('No interval given');

        new Validate(self::$feedsInvalid['emptyInterval']);
    }

    /**
     * Test validator with feed entry with an empty gotify token value
     */
    public function testValidateWithEmptyGotifyTokenEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('Empty Gotify token given');

        new Validate(self::$feedsInvalid['emptyGotifyToken']);
    }

    /**
     * Test validator with feed entry with an empty gotify priority value
     */
    public function testValidateWithEmptyGotifyPriorityEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('Empty Gotify priority given');

        new Validate(self::$feedsInvalid['emptyGotifyPriority']);
    }

    /**
     * Test validator with feed entry with an empty ntfy topic value
     */
    public function testValidateWithEmptyNtfyTopicEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('Empty Ntfy topic given');

        new Validate(self::$feedsInvalid['emptyNtfyTopic']);
    }

    /**
     * Test validator with feed entry with an empty ntfy priority value
     */
    public function testValidateWithEmptyNtfyPriorityEntry(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('Empty Ntfy priority given');

        new Validate(self::$feedsInvalid['emptyNtfyPriority']);
    }
}
