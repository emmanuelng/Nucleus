<?php

declare(strict_types=1);

namespace Nucleus\Json;

use ArrayAccess;
use ArrayIterator;
use Nucleus\Schema\Exceptions\UnknownFieldException;
use Nucleus\Schema\Schema;
use Traversable;

/**
 * Reprensents a JSON object.
 */
class JsonObject implements ArrayAccess
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
        return json_encode($this->values);
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
     * Sets the values of this values based with another iterable.
     *
     * @param iterable $values The values to set.
     * @param boolean $ignoreNull If true, skips the null values.
     * @return void
     */
    public function setAll(iterable $values, bool $ignoreNull = false): void
    {
        foreach ($values as $offset => $value) {
            if (!$ignoreNull || $value !== null) {
                $this[$offset] = $value;
            }
        }
    }

    /**
     * Returns the values contained by this object as an associative array.
     *
     * @return array The object's values.
     */
    public function values(): array
    {
        return $this->values;
    }
}
