<?php

declare(strict_types=1);

namespace Tests\Filter\Types;

use Nucleus\Types\Type;
use Nucleus\Types\Types\IntegerType;
use Tests\Types\TypeTest as TypesTypeTest;

class IntegerTypeTest extends TypesTypeTest
{
    /**
     * {@inheritDoc}
     */
    protected function type(): Type
    {
        return IntegerType::get();
    }

    /**
     * {@inheritDoc}
     */
    public function validValuesProvider(): array
    {
        return [
            "Integers"        => [1, 1],
            "Integer strings" => ['123', 123],
            "Null"            => [null, null]
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function invalidValuesProvider(): array
    {
        return [
            "Booleans"             => [true],
            "Strings with letters" => ['1a'],
            "Floats"               => [1.0],
            "Arrays"               => [['1']]
        ];
    }
}
