<?php

declare(strict_types=1);

namespace Tests\Filter\Types;

use Nucleus\Types\Exceptions\InvalidValueException;
use PHPUnit\Framework\TestCase;
use Tests\Types\Classes\TestSchema;

/**
 * Tests the schema object.
 */
class SchemaTest extends TestCase
{
    /**
     * Tests that valid values correctly filtered.
     *
     * @return void
     */
    public function testFiltersObjectValues()
    {
        $schema = new TestSchema([
            'integer'      => ['type' => 'int'],
            'boolean'      => ['type' => 'bool'],
            'float'        => ['type' => 'float'],
            'string'       => ['type' => 'string'],
            'object'       => ['type' => ['a' => ['type' => 'int']]],
            'integer list' => ['type' => 'int', 'isList' => true],
            'boolean list' => ['type' => 'bool', 'isList' => true],
            'float list'   => ['type' => 'float', 'isList' => true],
            'string list'  => ['type' => 'string', 'isList' => true],
            'object list'  => ['type' => ['a' => ['type' => 'int']], 'isList' => true],
        ]);

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

        $filtered = $schema->filter($object);
        foreach ($filtered as $key => $value) {
            $this->assertSame($object[$key], $value);
        }
    }

    /**
     * Tests that the schema supports default fields correctly.
     *
     * @return void
     */
    public function testAcceptsObjectsWithMissingOptionalValues()
    {
        $schema   = new TestSchema(['a' => ['type' => 'int', 'default' => 1]]);
        $filtered = $schema->filter([]);
        $this->assertSame($filtered, ['a' => 1]);
    }

    /**
     * Tests that the schema rejects objects with missing mandatory fields.
     *
     * @return void
     */
    public function testRejectsObjectsWithMissingMandatoryValues()
    {
        $schema = new TestSchema(['a' => ['type' => 'int']]);
        $this->expectException(InvalidValueException::class);
        $schema->filter([]);
    }
}
