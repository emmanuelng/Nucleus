<?php

declare(strict_types=1);

namespace Tests\Filter\Types;

use Tests\Filter\TypeTest;

class IntegerTypeTest extends TypeTest
{
    /**
     * {@inheritDoc}
     */
    protected function type()
    {
        return 'int';
    }

    /**
     * {@inheritDoc}
     */
    public function validValuesProvider(): array
    {
        return [
            "Integers"        => [1, 1],
            "Integer strings" => ['123', 123]
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
            "Arrays"               => [['1']],
            "Null"                 => [null]
        ];
    }
}
