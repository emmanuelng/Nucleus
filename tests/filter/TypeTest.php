<?php

declare(strict_types=1);

namespace Tests\Filter;

use Nucleus\Filter\Exceptions\InvalidValueException;
use Nucleus\Filter\Filter;
use PHPUnit\Framework\TestCase;

abstract class TypeTest extends TestCase
{
    abstract protected function type();
    abstract public function validValues(): array;
    abstract public function invalidValues(): array;

    /**
     * @dataProvider validValues
     */
    public function testAcceptsValidValues($value, $result): void
    {
        try {
            $filtered = Filter::value($this->type(), $value);
            $this->assertSame($result, $filtered);
        } catch (InvalidValueException $e) {
            $value = $e->value();
            $this->fail("Could not filter value $value.");
        }
    }

    /**
     * @dataProvider invalidValues
     */
    public function testRejectsInvalidValues($value): void
    {
        $this->expectException(InvalidValueException::class);
        Filter::value($this->type(), $value);
    }
}
