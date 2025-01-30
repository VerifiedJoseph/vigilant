<?php

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use Symfony\Component\Yaml\Exception\ParseException;
use Vigilant\Feed\Validate;
use Vigilant\Exception\FeedsException;
use Symfony\Component\Yaml\Yaml;

#[CoversClass(Validate::class)]
#[UsesClass(FeedsException::class)]
#[UsesClass(Vigilant\Helper\Time::class)]
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
     * Test valid feed entry
     */
    #[DoesNotPerformAssertions]
    public function testValidEntry(): void
    {
        $feeds = Yaml::parse(self::loadSample('feeds.yaml'));

        new Validate($feeds['feeds'][0], self::$minCheckInterval);
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
