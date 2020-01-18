<?php

declare(strict_types=1);

namespace Tests\Filter\Types;

use Nucleus\Types\Exceptions\InvalidValueException;
use Nucleus\Types\Field;
use Nucleus\Types\Schema;
use Nucleus\Types\Type;
use Nucleus\Types\Types\BooleanType;
use Nucleus\Types\Types\FloatType;
use Nucleus\Types\Types\IntegerType;
use Nucleus\Types\Types\StringType;
use PHPUnit\Framework\TestCase;

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
        $schema = self::buildSchema([
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
        $schema   = self::buildSchema(['a' => ['type' => 'int', 'default' => 1]]);
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
        $schema = self::buildSchema(['a' => ['type' => 'int']]);
        $this->expectException(InvalidValueException::class);
        $schema->filter([]);
    }

    /**
     * Builds a schema object from an array.
     *
     * @param array $schemaArr The array.
     * @return Schema The schema object.
     */
    private static function buildSchema(array $schemaArr): Schema
    {
        $schema = new Schema();

        foreach ($schemaArr as $name => $fieldArr) {
            $type   = self::buildType($fieldArr['type']);
            $isList = $fieldArr['isList'] ?? false;
            $field  = new Field($name, $type, $isList);

            if (isset($fieldArr['default'])) {
                $default = $fieldArr['default'];
                $field->setDefaultValue($default);
            }

            $schema->addField($field);
        }

        return $schema;
    }

    /**
     * Builds a type object based on an input. If the input is an array,
     * returns a schema, else returns a base type, if it exists.
     *
     * @param string|array $type The input.
     * @return Type|null The type object or nullif it is impossible to build
     * it.
     */
    private static function buildType($type): ?Type
    {
        // Array. Return a schema.
        if (is_array($type)) {
            return self::buildSchema($type);
        }

        // Other. Try to return a built-in type.
        switch ($type) {
            case 'int':
                return IntegerType::get();
            case 'bool':
                return BooleanType::get();
            case 'float':
                return FloatType::get();
            case 'string':
                return StringType::get();
            default:
                break;
        }

        // Invalid type.
        return null;
    }
}
