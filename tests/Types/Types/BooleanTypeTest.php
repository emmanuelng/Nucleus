<?php

declare(strict_types=1);

namespace Tests\Types\Types;

use Nucleus\Types\Type;
use Nucleus\Types\Types\BooleanType;
use Tests\Types\TypeTest as TypesTypeTest;

class BooleanTypeTest extends TypesTypeTest
{
    /**
     * {@inheritDoc}
     */
    protected function type(): Type
    {
        return BooleanType::get();
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
            "Null"               => [null, null]
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
            "Arrays"                  => [['true']]
        ];
    }
}
