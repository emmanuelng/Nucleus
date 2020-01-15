<?php

declare(strict_types=1);

namespace Tests\Types;

use Nucleus\Types\Exceptions\InvalidValueException;
use Nucleus\Types\Type;
use PHPUnit\Framework\TestCase;

abstract class TypeTest extends TestCase
{
    /**
     * Returns the tested type.
     *
     * @return Type
     */
    abstract protected function type() : Type;

    /**
     * Valid values data provider.
     *
     * @return array
     */
    abstract public function validValuesProvider(): array;

    /**
     * Invalid values data provider.
     *
     * @return array
     */
    abstract public function invalidValuesProvider(): array;

    /**
     * Tests that the filter accepts the valid values.
     *
     * @dataProvider validValuesProvider
     */
    public function testAcceptsValidValues($value, $result): void
    {
        try {
            $filtered = $this->type()->filter($value);
            $this->assertSame($result, $filtered);
        } catch (InvalidValueException $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * Tests that the filter rejects the invalid values.
     *
     * @dataProvider invalidValuesProvider
     */
    public function testRejectsInvalidValues($value): void
    {
        $this->expectException(InvalidValueException::class);
        $this->type()->filter($value);
    }
}
