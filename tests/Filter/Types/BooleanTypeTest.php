<?php

declare(strict_types=1);

namespace Tests\Filter\Types;

use Tests\Filter\TypeTest;

class BooleanTypeTest extends TypeTest
{
    /**
     * {@inheritDoc}
     */
    protected function type()
    {
        return 'bool';
    }

    /**
     * {@inheritDoc}
     */
    public function validValuesProvider(): array
    {
        return [
            "Booleans (true)"    => [true, true],
            "Booleans (false)"   => [false, false],
            "Strings ('true')"   => ['true', true],
            "Strings ('TRUE')"   => ['TRUE', true],
            "Strings ('True')"   => ['True', true],
            "Strings ('TrUe')"   => ['TrUe', true],
            "Strings ('false')"  => ['false', false],
            "Strings ('FALSE')"  => ['FALSE', false],
            "Strings ('False')"  => ['False', false],
            "Strings ('FaLse')"  => ['FaLse', false],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function invalidValuesProvider(): array
    {
        return [
            "Invalid strings ('abc')" => ['abc'],
            "Invalid strings ('1')"   => ['1'],
            "Invalid strings ('0')"   => ['0'],
            "Integers"                => [123],
            "Integers (1)"            => [1],
            "Integers (0)"            => [0],
            "Floats"                  => [1.5],
            "Arrays"                  => [['true']],
            "Null"                    => [null]
        ];
    }
}
