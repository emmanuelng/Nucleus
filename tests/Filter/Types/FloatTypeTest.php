<?php

declare(strict_types=1);

namespace Tests\Filter\Types;

use Tests\Filter\TypeTest;

class FloatTypeTest extends TypeTest
{
    /**
     * {@inheritDoc}
     */
    protected function type()
    {
        return 'float';
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
            "Integer strings" => ['123', 123.0]
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
            "Arrays"               => [[1.5]],
            "Null"                 => [null]
        ];
    }
}
