<?php

declare(strict_types=1);

namespace Tests\Types\Types;

use Nucleus\Types\Type;
use Nucleus\Types\Types\FloatType;
use Tests\Types\TypeTest as TypesTypeTest;

class FloatTypeTest extends TypesTypeTest
{
    /**
     * {@inheritDoc}
     */
    protected function type(): Type
    {
        return FloatType::get();
    }

    /**
     * {@inheritDoc}
     */
    public function validValuesProvider(): array
    {
        return [
            "Floats"          => [1.5, 1.5],
            "Float strings"   => ['12.678', 12.678],
            "Integers"        => [1, 1.0],
            "Integer strings" => ['123', 123.0],
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
            "Arrays"               => [[1.5]]
        ];
    }
}
