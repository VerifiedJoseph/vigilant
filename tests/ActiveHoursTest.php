<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Vigilant\ActiveHours;
use Vigilant\Logger;
use DateTime;
use DateTimeZone;

#[CoversClass(ActiveHours::class)]
#[UsesClass(Logger::class)]
class ActiveHoursTest extends TestCase
{
    private static Logger $logger;
    private static string $timezone = 'UTC';
    private string $formatRegex = '/[0-9]{4}+\-[0-9]{2}+\-[0-9]{2}+ [0-9]{2}+\:[0-9]{2}+\:[0-9]{2}+ UTC/';

    public static function setUpBeforeClass(): void
    {
        self::$logger = new Logger(self::$timezone);
    }

    /**
     * Test class with time that is inside the active window
     */
    public function testActiveHoursInWindow(): void
    {
        $now = new DateTime('10:00', new DateTimeZone(self::$timezone));

        $activeHours = new ActiveHours(
            $now,
            '06:00',
            '20:00',
            self::$timezone,
            self::$logger
        );

        $this->assertTrue($activeHours->isEnabled());
    }

    /**
     * Test class with time that is outside the active window
     */
    public function testActiveHoursOutsideWindow(): void
    {
        $now = new DateTime('05:00', new DateTimeZone(self::$timezone));

        $activeHours = new ActiveHours(
            $now,
            '06:00',
            '20:00',
            self::$timezone,
            self::$logger
        );

        $this->assertFalse($activeHours->isEnabled());
    }

    /**
     * Test date and time formats returned by getStartTime() and getEndTime() with a regex
     */
    public function testGetMethodsDateAndTimeFormats(): void
    {
        $now = new DateTime('07:00', new DateTimeZone(self::$timezone));

        $activeHours = new ActiveHours(
            $now,
            '06:00',
            '20:00',
            self::$timezone,
            self::$logger
        );

        $this->assertMatchesRegularExpression(
            $this->formatRegex,
            $activeHours->getStartTime()
        );

        $this->assertMatchesRegularExpression(
            $this->formatRegex,
            $activeHours->getEndTime()
        );
    }
}
