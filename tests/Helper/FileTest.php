<?php

use MockFileSystem\MockFileSystem as mockfs;
use Vigilant\Helper\File;
use Vigilant\Exception\AppException;

class FileTest extends TestCase
{
    private static string $tempFilePath;

    public function setup(): void
    {
        mockfs::create();
    }

    public function tearDown(): void
    {
        stream_context_set_default(
            [
                'mfs' => [
                    'fread_fail' => false,
                    'fwrite_fail' => false,
                    'fopen_fail' => false
                ]
            ]
        );
    }

    public static function setUpBeforeClass(): void
    {
        self::$tempFilePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'feeds.yaml';
    }

    /**
     * Test exists()
     */
    public function testExists(): void
    {
        $path = self::getSamplePath('feeds.yaml');
        self::assertEquals(true, File::exists($path));
    }

    /**
     * Test exists() when file does not exist.
     */
    public function testExistsFalse(): void
    {
        $path = mockfs::getUrl('/test.file');
        self::assertEquals(false, File::exists($path));
    }

    /**
     * Test read()
     */
    public function testRead(): void
    {
        $path = self::getSamplePath('feeds.yaml');
        $data = self::loadSample('feeds.yaml');

        self::assertEquals($data, File::read($path));
    }

    /**
     * Test `read()` file not read exception.
     */
    public function testReadNotReadException(): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage('File not read');

        $file = mockfs::getUrl('/test.file');
        file_put_contents($file, uniqid());

        $this->setStreamContext(['fread_fail' => true]);

        File::read($file);
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
        $data = self::loadSample('feeds.yaml');

        File::write(self::$tempFilePath, $data);

        self::assertEquals($data, File::exists(self::$tempFilePath));
        self::assertEquals($data, File::read(self::$tempFilePath));
    }

    /**
     * Test `write()` file not written exception.
     */
    public function testWriteNotWrittenException(): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage('File not written');

        $file = mockfs::getUrl('/test1.file');
        file_put_contents($file, uniqid());

        $this->setStreamContext(['fwrite_fail' => true]);

        File::write($file, 'hello');
    }

    public static function tearDownAfterClass(): void
    {
        unlink(self::$tempFilePath);
    }

    /**
     * Set stream context defaults for `MockFileSystem\MockFileSystem`
     *
     * @param array<string, boolean> $options
     */
    private function setStreamContext(array $options): void
    {
        stream_context_set_default([
            'mfs' => $options
        ]);
    }
}
