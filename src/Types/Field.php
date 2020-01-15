<?php

declare(strict_types=1);

namespace Nucleus\Types;

use Nucleus\Types\Exceptions\InvalidValueException;

/**
 * Represents a schema field.
 */
class Field
{
    /**
     * The name of the field.
     *
     * @var string
     */
    private $name;

    /**
     * The type of the field.
     *
     * @var Type
     */
    private $type;

    /**
     * Indicates whether the field is a list.
     *
     * @var bool
     */
    private $isList;

    /**
     * The default value of the field.
     *
     * @var mixed
     */
    private $defaultValue;

    /**
     * Initializes the field.
     *
     * @param string $name The name of the field.
     * @param Type $type The type of the field.
     * @param bool $isList Indicated whether the field is a list.
     */
    public function __construct(
        string $name,
        Type $type,
        bool $isList
    ) {
        $this->name   = $name;
        $this->type   = $type;
        $this->isList = $isList;
    }

    /**
     * Sets the default value of the field.
     *
     * @param mixed $defaultValue
     * @return void
     */
    public function setDefaultValue($defaultValue): void
    {
        // Null is always a valid default value.
        if ($defaultValue === null) {
            $this->defaultValue = null;
            return;
        }

        // Filter the default value to make sure it is compatible with the type
        // of the field.
        $this->defaultValue = $this->isList
            ? $this->filterList($defaultValue)
            : $this->filterValue($defaultValue);
    }

    /**
     * Returns the name of the field.
     *
     * @return string The name.
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Filters a value base for this field.
     *
     * @param mixed $value The value.
     * @return mixed The filtered value.
     */
    public function filter($value)
    {
        // The value is not empty.
        if ($value !== null) {
            return $this->isList ?
                $this->filterList($value) : $this->filterValue($value);
        }

        // Empty value. Check if the field has a default value.
        if (!isset($this->defaultValue)) {
            $msg = "Missing value.";
            throw new InvalidValueException($msg);
        }

        // Return the default value.
        return $this->defaultValue;
    }

    /**
     * Filters a list of values.
     *
     * @param mixed $value The value to filter. Must be an array.
     * @return mixed The filtered value.
     */
    private function filterList($value)
    {
        if (!is_array($value)) {
            $msg = "'$this->name' must be a list.";
            throw new InvalidValueException($msg);
        }

        $filtered = [];
        foreach ($value as $item) {
            $filtered[] = $this->filterValue($item);
        }

        return $filtered;
    }

    /**
     * Filters a simple value.
     *
     * @param mixed $value The value to filter.
     * @return mixed The filtered value.
     */
    private function filterValue($value)
    {
        return $this->type->filter($value);
    }
}
