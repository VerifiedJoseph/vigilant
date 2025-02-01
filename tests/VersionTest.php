<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\CoversClass;
use Vigilant\Version;

#[CoversClass(Version::class)]
class VersionTest extends TestCase
{
    private static string $expectedVersion;
    private static int $expectedCacheVersion;

    public static function setUpBeforeClass(): void
    {
        $reflection = new ReflectionClass(new Version());
        self::$expectedVersion = $reflection->getProperty('version')->getValue();
        self::$expectedCacheVersion = $reflection->getProperty('cacheFormatVersion')->getValue();
    }

    public function testGet(): void
    {
        $this->assertEquals(self::$expectedVersion, Version::get());
    }

    public function testGetCacheFormatVersion(): void
    {
        $this->assertEquals(self::$expectedCacheVersion, Version::getCacheFormatVersion());
    }
}
