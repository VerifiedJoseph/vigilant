<?php

use PHPUnit\Framework\Attributes\CoversClass;
use Vigilant\Version;

#[CoversClass(Version::class)]
class VersionTest extends TestCase
{
    private static string $expected;

    public static function setUpBeforeClass(): void
    {
        $reflection = new ReflectionClass(new Version());
        self::$expected = $reflection->getProperty('version')->getValue();
    }

    public function testGet(): void
    {
        $this->assertEquals(self::$expected, Version::get());
    }
}
