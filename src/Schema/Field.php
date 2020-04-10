<?php

declare(strict_types=1);

namespace Nucleus\Schema;

use Nucleus\Schema\Exceptions\InvalidSchemaException;
use Nucleus\Schema\Exceptions\InvalidValueException;
use Nucleus\Schema\Exceptions\UnknownTypeException;
use Nucleus\Schema\Types\BooleanType;
use Nucleus\Schema\Types\NumberType;
use Nucleus\Schema\Types\StringType;

/**
 * Represents a field of an object.
 */
class Field
{
    /**
     * Builds a field based on an array.
     *
     * @param string $name The field's name.
     * @param array $array The array.
     * @return Field The built field object.
     */
    public static function loadFromArray(string $name, array $array): Field
    {
        // Get the field's type
        $type = $array['type'] ?? null;
        if ($type == null) {
            throw new InvalidSchemaException('Missing type.');
        }

        if (is_array($type)) {
            $typeObj = Schema::loadFromArray($type);
        } else if (is_string($type)) {
            $typeObj = self::getBaseType($type);
        } else {
            throw new InvalidSchemaException('Invalid type.');
        }

        // Create the field
        $field = new Field($name, $typeObj);

        $field->required = $array['required'] ?? false;
        $field->list     = $array['isList'] ?? false;
        $field->hidden   = $array['hidden'] ?? false;

        return ($field);
    }

    /**
     * Finds and returns a base (built-in) type.
     *
     * @param string $name The base type name.
     * @return Type The base type object.
     */
    private static function getBaseType(string $name): Type
    {
        switch ($name) {
            case 'number':
                return new NumberType();
            case 'boolean':
                return new BooleanType();
            case 'string':
                return new StringType();
            default:
                break;
        }

        throw new UnknownTypeException($name);
    }

    /**
     * The field's name.
     *
     * @var string
     */
    private $name;

    /**
     * The field's type.
     *
     * @var Type
     */
    private $type;

    /**
     * Indicates whether this field is a list.
     *
     * @var bool
     */
    private $list;

    /**
     * Indicates whether this field is required.
     *
     * @var bool
     */
    private $required;

    /**
     * Indicates whether this field must be hidden from the public.
     *
     * @var bool
     */
    private $hidden;

    /**
     * Initializes the field.
     *
     * @param string $name The field's name.
     * @param Type $type The field's type.
     */
    private function __construct(string $name, Type $type)
    {
        $this->name     = $name;
        $this->type     = $type;
        $this->list     = false;
        $this->required = false;
        $this->hidden   = false;
    }

    /**
     * Returns the field's name.
     *
     * @return string The name.
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Checks whether a value is compatible with the field's definition and
     * returns the filtered value.
     *
     * @param mixed $value The value to filter.
     * @return mixed The filtered value.
     */
    public function filter($value)
    {
        if ($value === null) {
            if ($this->required) {
                $msg = "The field '$this->name' is required.";
                throw new InvalidValueException($msg);
            }
        } else {
            if ($this->list) {
                return $this->filterList($value);
            } else {
                return $this->type->filter($value);
            }
        }
    }

    /**
     * Returns whether this field must be hidden from the public.
     *
     * @return boolean True if the field is hidden, false otherwise.
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * Returns whether this field is required or not.
     *
     * @return boolean True if the field is required, false otherwise.
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * Filters a list value.
     *
     * @param mixed $value The value to filter.
     * @return mixed The filtered value.
     */
    private function filterList($value)
    {
        if (!is_iterable($value)) {
            $msg = 'The field must be a list.';
            throw new InvalidValueException($msg);
        }

        if ($value === null) {
            return null;
        }

        $filtered = [];
        foreach ($value as &$item) {
            $filtered[] = $this->type->filter($item);
        }

        return $filtered;
    }
}
