<?php

declare(strict_types=1);

namespace Nucleus\Schema;

use Exception;
use Nucleus\Schema\Exceptions\InvalidValueException;

/**
 * Describes the fields and the values of an object.
 */
class Schema implements Type
{
    /**
     * The schema's fields.
     *
     * @var array
     */
    private $fields;

    /**
     * Initializes the schema.
     *
     * @param array $array The array representation of the schema.
     */
    public function __construct(array $array)
    {
        $this->fields = [];

        foreach ($array as $fieldName => $fieldArr) {
            $this->addField(new Field($fieldName, $fieldArr));
        }
    }

    /**
     * Returns the schema's fields.
     *
     * @return array An array of fields.
     */
    public function fields(): array
    {
        return array_values($this->fields);
    }

    /**
     * Returns the field corresponding to the given name.
     *
     * @param string $name The field's name.
     * @return Field|null The field object or null if it doesn't exist.
     */
    public function field(string $name): ?Field
    {
        return $this->fields[$name] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function filter($value)
    {
        if (!is_array($value)) {
            $msg = "The value must be an associative array.";
            throw new InvalidValueException($msg);
        }

        $filtered = [];
        foreach ($this->fields as $name => $fieldObj) {
            $filtered[$name] = $fieldObj->filter($value[$name] ?? null);
        }

        return $filtered;
    }

    /**
     * Returns an array representing this schema.
     *
     * @return array The array.
     */
    public function toArray(): array
    {
        $array = [];

        foreach ($this->fields as $name => $field) {
            $array[$name] = $field->toArray();
        }

        return $array;
    }

    /**
     * Adds a field to the schema.
     *
     * @param Field $field The field to add.
     * @return void
     */
    private function addField(Field $field)
    {
        $this->fields[$field->name()] = $field;
    }
}
