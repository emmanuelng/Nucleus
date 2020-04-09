<?php

declare(strict_types=1);

namespace Nucleus\Schema;

use Nucleus\Schema\Exceptions\InvalidSchemaException;
use Nucleus\Schema\Exceptions\InvalidValueException;

/**
 * Describes the fields and the values of an object.
 */
class Schema implements Type
{
    /**
     * Builds a schema based on an array.
     *
     * @param array $array The array.
     * @return Schema The built schema.
     */
    public static function loadFromArray(array $array): Schema
    {
        $schema = new Schema();

        foreach ($array as $fieldName => $fieldArr) {
            $field = Field::loadFromArray($fieldName, $fieldArr);
            $schema->addField($field);
        }

        return $schema;
    }

    /**
     * Loads a schema from a JSON file.
     *
     * @param string $path The path to the schema file.
     * @return Schema The built schema
     */
    public static function loadFromFile(string $path): Schema
    {
        $content = file_get_contents($path);
        $json    = json_decode($content, true);

        if ($json === NULL) {
            throw new InvalidSchemaException($path);
        }

        return self::loadFromArray($json);
    }

    /**
     * The schema's fields.
     *
     * @var array
     */
    private $fields;

    /**
     * Constructs an empty schema.
     */
    private function __construct()
    {
        $this->fields = [];
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
