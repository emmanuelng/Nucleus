<?php

declare(strict_types=1);

namespace Nucleus\Types;

/**
 * Represents a schema.
 */
abstract class Schema implements Type
{
    /**
     * The fields of the schema.
     *
     * @var array
     */
    private $fields = [];

    /**
     * Adds a field to the schema.
     *
     * @param Field $field The field to add.
     * @return void
     */
    protected function addField(Field $field): void
    {
        $this->fields[$field->name()] = $field;
    }

    /**
     * {@inheritDoc}
     */
    public function filter($value)
    {
        // Accept null values.
        if ($value === null) {
            return null;
        }

        // Only accept arrays.
        if (!is_array($value)) {
            return false;
        }

        // Validate each field of the schema.
        $filtered = [];
        foreach ($this->fields as $name => $field) {
            $fieldValue = $value[$name] ?? null;
            $filtered[$name] = $field->filter($fieldValue);
        }

        // Return the filtered value.
        return $filtered;
    }
}
