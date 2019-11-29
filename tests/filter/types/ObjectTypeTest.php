<?php

declare(strict_types=1);

namespace Tests\Filter\Types;

use Nucleus\Filter\Exceptions\MissingPropertyException;
use Nucleus\Filter\Exceptions\MissingTypeException;
use Nucleus\Filter\Filter;
use PHPUnit\Framework\TestCase;

class ObjectTypeTest extends TestCase
{
    public function testFiltersObjectValues()
    {
        $schema = [
            'integer'      => ['type' => 'int'],
            'boolean'      => ['type' => 'bool'],
            'float'        => ['type' => 'float'],
            'string'       => ['type' => 'string'],
            'object'       => ['type' => ['a' => ['type' => 'int']]],
            'integer list' => ['type' => 'int', 'isList' => true],
            'boolean list' => ['type' => 'bool', 'isList' => true],
            'float list'   => ['type' => 'float', 'isList' => true],
            'string list'  => ['type' => 'string', 'isList' => true],
            'object list'  => ['type' => ['a' => ['type' => 'int']] , 'isList' => true],
        ];

        $object = [
            'integer'      => 1,
            'boolean'      => true,
            'float'        => 1.5,
            'string'       => 'abc',
            'object'       => ['a' => 1],
            'integer list' => [1, 2, 3],
            'boolean list' => [true, false],
            'float list'   => [1.5, 2.5, 3.5],
            'string list'  => ['abc', 'def'],
            'object list'  => [['a' => 1], ['a' => 2]],
        ];

        $filtered = Filter::filterValue($schema, $object);
        foreach ($filtered as $key => $value) {
            $this->assertSame($object[$key], $value);
        }
    }

    public function testAcceptsObjectsWithMissingOptionalValues() {
        $schema   = ['a' => ['type' => 'int', 'default' => 1]];
        $filtered = Filter::filterValue($schema, []);
        $this->assertSame($filtered, ['a' => 1]);
    }

    public function testRejectsObjectsWithMissingMandatoryValues()
    {
        $schema = ['a' => ['type' => 'int']];
        $this->expectException(MissingPropertyException::class);
        Filter::filterValue($schema, []);
    }

    public function testThrowsExceptionWhenNoTypeIsDefined()
    {
        $schema = ['a' => []];
        $this->expectException(MissingTypeException::class);
        Filter::filterValue($schema, []);
    }
}
