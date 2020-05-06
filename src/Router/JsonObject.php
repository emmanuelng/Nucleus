<?php

declare(strict_types=1);

namespace Nucleus\Router;

use ArrayAccess;
use ArrayIterator;
use JsonSerializable;
use Nucleus\Schema\Exceptions\UnknownFieldException;
use Nucleus\Schema\Schema;
use Traversable;

/**
 * Reprensents a JSON object.
 */
class JsonObject implements ArrayAccess, JsonSerializable
{
    /**
     * The schema.
     *
     * @var JsonSchema
     */
    private $schema;

    /**
     * The underlying JSON values.
     *
     * @var array
     */
    private $values;

    /**
     * Initializes a JSON entity.
     *
     * @param array $values The initial values.
     * @param Schema|null $schema The schema
     */
    public function __construct(array $values = [], ?Schema $schema = null)
    {
        $this->schema = $schema;
        $this->values = $schema === null ? $values : $schema->filter($values);
    }

    /**
     * Returns the JSON representation of this object.
     *
     * @return string The JSON representation.
     */
    public function __toString()
    {
        return json_encode($this->jsonSerialize());
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        // If there is no schema, return the values directly.
        if ($this->schema === null) {
            return $this->values;
        }

        // Initialize the result.
        $resultArr = [];

        // Remove hidden fields.
        foreach ($this->values as $fieldName => $value) {
            $field = $this->schema->field($fieldName);
            if ($field != null && !$field->isHidden()) {
                $resultArr[$fieldName] = $value;
            }
        }

        return $resultArr;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset): bool
    {
        if ($this->schema === null) {
            return array_key_exists($offset, $this->values);
        }

        if (is_string($offset)) {
            return $this->schema->field($offset) !== null;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        if ($this->schema !== null && $this->schema->field($offset) === null) {
            throw new UnknownFieldException($offset);
        }

        return $this->values[$offset];
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        if ($this->schema === null) {
            $this->values[$offset] = $value;
            return;
        }

        $field = $this->schema->field($offset);
        if ($field === null) {
            throw new UnknownFieldException($offset);
        }

        $this->values[$offset] = $field->filter($value);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        if ($this->schema !== null && $this->schema->field($offset) === null) {
            throw new UnknownFieldException($offset);
        }

        unset($this->values[$offset]);
    }

    /**
     * Returns the values stored in the object.
     *
     * @return array The values in an associative array.
     */
    public function getValues(): array
    {
        return $this->values;
    }
}
