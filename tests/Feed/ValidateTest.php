<?php

declare(strict_types=1);

namespace Tests\Feed;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Vigilant\Feed\Validate;
use Vigilant\Exception\FeedsException;
use Symfony\Component\Yaml\Yaml;

#[CoversClass(Validate::class)]
#[UsesClass(FeedsException::class)]
#[UsesClass(\Vigilant\Helper\Time::class)]
class ValidateTest extends TestCase
{
    /**
     * @var int $minCheckInterval Minimum feed check interval in seconds
     */
    private static int $minCheckInterval = 300;

    /**
     * @return array<int, mixed>
     */
    public static function invalidFeedDataProvider(): array
    {
        $data = [];
        $yaml = Yaml::parse(self::loadSample('feeds-invalid.yaml'));

        foreach ($yaml['feeds'] as $item) {
            $data[] = [
                $item['data'], $item['exception']
            ];
        }

        return $data;
    }

    /**
     * @return array<int, mixed>
     */
    public static function validFeedDataProvider(): array
    {
        $data = [];
        $yaml = Yaml::parse(self::loadSample('feeds.yaml'));


        foreach ($yaml['feeds'] as $item) {
            $data[] = [$item];
        }

        return  $data;
    }

    /**
     * Test valid feed entries
     *
     * @param array<string, mixed> $data Feed entry data
     */
    #[DataProvider('validFeedDataProvider')]
    public function testValidEntries(array $data): void
    {
        $validate = new Validate($data, self::$minCheckInterval);
        $this->assertEquals($data, $validate->get());
    }

    /**
     * Test invalid feed entries
     *
     * @param array<string, mixed> $data Feed entry data
     * @param string $exception Exception message
     */
    #[DataProvider('invalidFeedDataProvider')]
    public function testInvalidFeedEntry(array $data, string $exception): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage($exception);

        new Validate($data, self::$minCheckInterval);
    }
}
