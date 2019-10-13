<?php

declare(strict_types=1);

namespace Nucleus\Filter\Types;

use Nucleus\Filter\Exceptions\InvalidValueException;
use Nucleus\Filter\Exceptions\MissingPropertyException;
use Nucleus\Filter\Exceptions\MissingTypeException;
use Nucleus\Filter\Filter;
use Nucleus\Filter\Type;

/**
 * ### ObjectType class
 *
 * Represents an object type. An object is an aggregation of base type values
 * and/or other objects. The structure of an object is defined by a schema.
 */
class ObjectType implements Type
{
    /**
     * The object's properties.
     *
     * @var array
     */
    private $properties;

    /**
     * Initializes an object type.
     * The structure of the object is defined by a schema. A schema is an
     * associative array where each key defines a property. For instance, a
     * Person schema, might have the properties 'name' or 'age'. Each
     * property must be associated with an associative array with the
     * following keys:
     *   - **type** *(mandatory)*: The property's type. It can either be a
     *     string (e.g. 'int', 'string', etc.) or another schema.
     *   - **isList** *(optional)*: Must be `true` if the property is a
     *     list. Is `false` by default.
     *   - **default** *(optional)*: Gives the default value of the property.
     *     If not defined, the property is considered as mandatory, i.e. null
     *     values won't be accepted for this property.
     *
     * Schema example:
     * ```
     * [
     *    'name' => [
     *       'type'    => 'string',
     *       'default' => ''
     *    ],
     *    'friends' => [
     *       'type     => [
     *          'name' => ['type' => 'string'],
     *          'age' => ['type' => 'int']
     *       ],
     *       'isList'  => true,
     *       'default' => []
     *    ]
     * ]
     * ```
     *
     * @param array $schema The schema.
     */
    public function __construct(array $schema)
    {
        $this->properties = [];

        // Parse the schema
        foreach ($schema as $name => $prop) {
            // Type must be defined
            if (!isset($prop['type'])) {
                throw new MissingTypeException($name);
            }

            // Add property
            $this->properties[$name] = [
                'type'    => $prop['type'],
                'isList'  => ($prop['isList'] ?? false) == true
            ];

            // Set default value
            if (isset($prop['default'])) {
                $this->properties[$name]['default'] = $prop['default'];
            }
        }
    }

    public function filter($value)
    {
        // The value must be an associative array
        if (!is_iterable($value)) {
            throw new InvalidValueException('object', $value);
        }

        $result = [];
        foreach ($this->properties as $name => $property) {
            $hasValue   = isset($value[$name]);
            $hasDefault = isset($property['default']);

            // Missing value
            if (!$hasValue && !$hasDefault) {
                throw new MissingPropertyException($name);
            }

            // Value exists
            $type    = $property['type'];
            $isList  = $property['isList'];
            $propVal = $hasValue ? $value[$name] : $property['default'];

            // Filter value (simple value)
            if (!$isList) {
                $result[$name] = Filter::value($type, $propVal);
                continue;
            }

            // Filter value (list)
            if (is_array($propVal)) {
                $result[$name] = Filter::list($type, $propVal);
                continue;
            }

            // Invalid value
            throw new InvalidValueException('object', $value);
        }

        return $result;
    }
}
