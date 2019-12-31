<?php

declare(strict_types=1);

namespace Tests\Filter\Types;

use Tests\Filter\TypeTest;

class StringTypeTest extends TypeTest
{
    /**
     * {@inheritDoc}
     */
    protected function type()
    {
        return 'string';
    }

    /**
     * {@inheritDoc}
     */
    public function validValuesProvider(): array
    {
        return [
            "Strings"       => ['abc', 'abc'],
            "Empty strings" => ['', ''],
            "Integers"      => [1, '1'],
            "Booleans"      => [true, 'true'],
            "Floats"        => [1.5, '1.5']
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function invalidValuesProvider(): array
    {
        return [
            "Null"  => [null],
            "Lists" => [['a', 'b', 'c']]
        ];
    }
}
