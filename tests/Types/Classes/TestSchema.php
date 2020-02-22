<?php

declare(strict_types=1);

namespace Tests\Types\Classes;

use Nucleus\Types\Field;
use Nucleus\Types\Schema;
use Nucleus\Types\Type;
use Nucleus\Types\Types\BooleanType;
use Nucleus\Types\Types\FloatType;
use Nucleus\Types\Types\IntegerType;
use Nucleus\Types\Types\StringType;

/**
 * A class used to test schemas
 */
class TestSchema extends Schema
{
    /**
     * Initializes the test schema.
     *
     * @param array $schemaArr An array representing the schema.
     */
    public function __construct(array $schemaArr = [])
    {
        foreach ($schemaArr as $name => $fieldArr) {
            $type   = self::getType($fieldArr['type']);
            $isList = $fieldArr['isList'] ?? false;

            if (isset($fieldArr['default'])) {
                $default = $fieldArr['default'];
                $field   = new Field($name, $type, $isList, $default);
            } else {
                $field = new Field($name, $type, $isList);
            }

            $this->addField($field);
        }
    }

    /**
     * Returns a type object based on an input. If the input is an array,
     * returns a schema, else returns a base type.
     *
     * @param string|array $type The input.
     * @return Type|null The type object or null
     */
    private static function getType($type): ?Type
    {
        // Array. Return a schema.
        if (is_array($type)) {
            return new TestSchema($type);
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
