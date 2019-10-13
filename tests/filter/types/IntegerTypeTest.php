<?php

declare(strict_types=1);

require_once(__DIR__ . '/../TypeTest.php');

class IntegerTypeTest extends TypeTest
{
    protected function type()
    {
        return 'int';
    }

    public function validValues(): array
    {
        return [
            "Integers"        => [1, 1],
            "Integer strings" => ['123', 123]
        ];
    }

    public function invalidValues(): array
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
