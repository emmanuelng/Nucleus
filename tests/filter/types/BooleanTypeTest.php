<?php

declare(strict_types=1);

require_once(__DIR__ . '/../TypeTest.php');

class BooleanTypeTest extends TypeTest
{
    protected function type()
    {
        return 'bool';
    }

    public function validValues(): array
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

    public function invalidValues(): array
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
