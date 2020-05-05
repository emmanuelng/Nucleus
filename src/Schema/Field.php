<?php

declare(strict_types=1);

namespace Nucleus\Schema;

use Exception;
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
     * The field's name.
     *
     * @var string
     */
    private $name;

    /**
     * The field's type.
     *
     * @var Type|Schema
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
     * @param array $array The field's array representation.
     */
    public function __construct(string $name, array $array)
    {
        $this->name     = $name;
        $this->type     = self::stringToType($array['type'] ?? null);
        $this->required = $array['required'] ?? false;
        $this->list     = $array['list'] ?? false;
        $this->hidden   = $array['hidden'] ?? false;
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
     * Returns the field's type.
     *
     * @return Type The type.
     */
    public function type(): Type
    {
        return $this->type;
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
     * Returns the array representation of the field.
     *
     * @return array The array representation.
     */
    public function toArray(): array
    {
        return [
            'type'     => self::typeToString($this->type),
            'required' => $this->required,
            'list'     => $this->list,
            'hidden'   => $this->hidden
        ];
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

    /**
     * Returns the string (or array) representation of a type.
     *
     * @return string|array The string of the array, or an array if the type is
     * a schema type.
     */
    private static function typeToString($type)
    {
        if ($type instanceof Schema) {
            return $type->toArray();
        }

        if ($type instanceof NumberType) {
            return 'number';
        }

        if ($type instanceof BooleanType) {
            return 'boolean';
        }

        if ($type instanceof StringType) {
            return 'string';
        }

        return null;
    }

    /**
     * Converts a string (or an array) to a type object.
     *
     * @param string|array $type A string representing the type (or an array if
     * it is a schema type).
     * @return Type The corresponding type object.
     */
    private static function stringToType($type): Type
    {
        if (is_array($type)) {
            return new Schema($type);
        }

        if ($type === 'number') {
            return new NumberType();
        }

        if ($type === 'boolean') {
            return new BooleanType();
        }

        if ($type == 'string') {
            return new StringType();
        }

        throw new UnknownTypeException(strval($type));
    }
}
