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
     * @param array $array The field's array representation.
     */
    public function __construct(string $name, array $array)
    {
        $this->name     = $name;
        $this->required = $array['required'] ?? false;
        $this->list     = $array['isList'] ?? false;
        $this->hidden   = $array['hidden'] ?? false;

        // Get the field's type
        $type = $array['type'] ?? null;
        if ($type == null) {
            throw new InvalidSchemaException('Missing type.');
        }

        $this->type = null;

        // The type is a schema.
        if (is_array($type)) {
            $this->type = new Schema($type);
        }

        // Simple type.
        if (is_string($type)) {
            switch ($type) {
                case 'number':
                    $this->type = new NumberType();
                    break;
                case 'boolean':
                    $this->type = new BooleanType();
                    break;
                case 'string':
                    $this->type = new StringType();
                    break;
                default:
                    break;
            }
        }

        // Type not found.
        if ($this->type === null) {
            throw new UnknownTypeException($type);
        }
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
