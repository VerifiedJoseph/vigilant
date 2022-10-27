<?php

use Vigilant\Helper\File;
use Vigilant\Exception\AppException;

class FileTest extends TestCase
{
    private static string $tempFilePath;

    public static function setUpBeforeClass(): void
    {
        self::$tempFilePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'feeds.yaml';
    }

    /**
     * Test exists()
     */
    public function testExists(): void
    {
        $path = self::getFixturePath('feeds.yaml');

        self::assertEquals(true, File::exists($path));
    }

    /**
     * Test exists() when file does not exist.
     */
    public function testExistsFalse(): void
    {
        self::assertEquals(false, File::exists('no-file-exists.yaml'));
    }

    /**
     * Test read()
     */
    public function testRead(): void
    {
        $path = self::getFixturePath('feeds.yaml');
        $data = self::loadFixture('feeds.yaml');

        self::assertEquals($data, File::read($path));
    }

    /**
     * Test read() file not opened exception.
     *
     * '@' is used suppress notices and errors from fopen()
     */
    public function testReadNotOpenedException(): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage('File not opened');

        @File::read('no-file-exists.yaml');
    }

    /**
     * Test write()
     */
    public function testWrite(): void
    {
        $data = self::loadFixture('feeds.yaml');

        File::write(self::$tempFilePath, $data);

        self::assertEquals($data, File::exists(self::$tempFilePath));
        self::assertEquals($data, File::read(self::$tempFilePath));
    }

    public static function tearDownAfterClass(): void
    {
        unlink(self::$tempFilePath);
    }
}
