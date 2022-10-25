<?php

use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Class TestCase
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Load fixture
     */
    protected static function loadFixture(string $name): string
    {
        $fixturePath = __DIR__ . '/Fixtures/' . $name;
        $contents = file_get_contents($fixturePath);

        if ($contents === false) {
            throw new RuntimeException(sprintf('Unable to load fixture: %s', $fixturePath));
        }

        return $contents;
    }
}
