<?php

declare(strict_types=1);

namespace Tests\Filter;

use Nucleus\Filter\Exceptions\InvalidValueException;
use Nucleus\Filter\Filter;
use PHPUnit\Framework\TestCase;

abstract class TypeTest extends TestCase
{
    /**
     * Returns the tested type.
     *
     * @return string|array
     */
    abstract protected function type();

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
            $filtered = Filter::filterValue($this->type(), $value);
            $this->assertSame($result, $filtered);
        } catch (InvalidValueException $e) {
            $value = $e->value();
            $this->fail("Could not filter value $value.");
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
        Filter::filterValue($this->type(), $value);
    }
}
