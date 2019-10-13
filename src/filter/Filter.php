<?php

declare(strict_types=1);

namespace Nucleus\Filter;

use Nucleus\Filter\Exceptions\InvalidTypeException;
use Nucleus\Filter\Types\BooleanType;
use Nucleus\Filter\Types\FloatType;
use Nucleus\Filter\Types\IntegerType;
use Nucleus\Filter\Types\ObjectType;
use Nucleus\Filter\Types\StringType;

/**
 * ### Filter class
 *
 * This class is used to filter values. Filtering is the process of validating
 * and casting a variable if needed.
 */
final class Filter
{
    /**
     * Filters a value.
     *
     * @param mixed $type The type name or schema.
     * @param mixed $value The value.
     * @return mixed The filtered value.
     */
    public static function value($type, $value)
    {
        $typeObj = self::getType($type);
        return $typeObj->filter($value);
    }

    /**
     * Filters a list.
     *
     * @param mixed $type The type name or schema.
     * @param mixed $value The value.
     * @return mixed The filtered value.
     */
    public static function list($type, $value)
    {
        if (!is_iterable($value)) {
            throw new InvalidTypeException($type);
        }

        $result = [];
        foreach ($value as $value) {
            $result[] = self::value($type, $value);
        }

        return $result;
    }

    /**
     * Returns an instance a Type object that corresponds to a type name or
     * schema.
     *
     * @param mixed $type The type name or a schema.
     * @return Type The type object.
     */
    private static function getType($type): Type
    {
        // The type is an object type
        if (is_array($type)) {
            return new ObjectType($type);
        }

        // The type is a base type
        if (is_string($type)) {
            return self::getBaseType($type);
        }

        // Invalid type
        throw new InvalidTypeException($type);
    }

    /**
     * Returns an instance a Type object that corresponds to a base type.
     *
     * @param string $type The base type name.
     * @return Type The base type.
     */
    private static function getBaseType(string $type): Type
    {
        static $baseTypes = [
            'int'    => IntegerType::class,
            'string' => StringType::class,
            'float'  => FloatType::class,
            'bool'   => BooleanType::class
        ];

        // The base type exists
        if (isset($baseTypes[$type])) {
            return new $baseTypes[$type];
        }

        // Invalid base type
        throw new InvalidTypeException($type);
    }
}
