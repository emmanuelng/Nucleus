<?php

declare(strict_types=1);

namespace Tests\Filter\Types;

use Nucleus\Types\Type;
use Nucleus\Types\Types\StringType;
use Tests\Types\TypeTest as TypesTypeTest;

class StringTypeTest extends TypesTypeTest
{
    /**
     * {@inheritDoc}
     */
    protected function type(): Type
    {
        return StringType::get();
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
            "Floats"        => [1.5, '1.5'],
            "Null"          => [null, null]
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function invalidValuesProvider(): array
    {
        return [
            "Lists"   => [['a', 'b', 'c']],
            "Objects" => [new class(){}]
        ];
    }
}
