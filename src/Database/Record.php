<?php

declare(strict_types=1);

namespace Nucleus\Database;

use ArrayAccess;
use JsonSerializable;
use Nucleus\Schema\Exceptions\UnknownFieldException;
use Nucleus\Schema\Schema;

/**
 * Represents a database record.
 */
class Record implements ArrayAccess, JsonSerializable
{
    /**
     * The schema.
     *
     * @var Schema
     */
    private $schema;

    /**
     * The underlying JSON values.
     *
     * @var array
     */
    private $values;

    /**
     * Initializes the record.
     *
     * @param Schema $schema The schema.
     * @param array $values The values.
     */
    public function __construct(Schema $schema, array $values = [])
    {
        $this->schema = $schema;
        $this->values = $values;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        $resultArr = [];

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
    public function offsetExists($offset): bool
    {
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
        if ($this->schema->field($offset) === null) {
            throw new UnknownFieldException($offset);
        }

        return $this->values[$offset];
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
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
        if ($this->schema->field($offset) === null) {
            throw new UnknownFieldException($offset);
        }

        unset($this->values[$offset]);
    }
}
