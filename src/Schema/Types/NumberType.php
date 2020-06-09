<?php

declare(strict_types=1);

namespace Nucleus\Schema\Types;

use Nucleus\Schema\Exceptions\InvalidValueException;
use Nucleus\Schema\Type;

/**
 * Number base type.
 */
class NumberType implements Type
{
    /**
     * {@inheritDoc}
     */
    public function filter($value)
    {
        if (!$this->filterInteger($value) && !$this->filterFloat($value)) {
            throw new InvalidValueException('Invalid number.');
        }

        return $value;
    }

    /**
     * Filters integer values.
     *
     * @param mixed $value A reference to the value to filter.
     *
     * @return boolean True if the value is valid, false otherwise.
     */
    public function filterInteger(&$value): bool
    {
        // Only accept integers and strings
        if (!is_int($value) && !is_string($value)) {
            return false;
        }

        // Filter the value
        $result = filter_var($value, FILTER_VALIDATE_INT);
        if ($result === false) {
            return false;
        }

        // Set the value
        $value = $result;
        return true;
    }

    /**
     * Filters floating-point values.
     *
     * @param mixed $value A reference to the value to filter.
     *
     * @return boolean True if the value is valid, false otherwise.
     */
    public function filterFloat(&$value): bool
    {
        // Only accept floats, integers and strings
        if (!is_float($value) && !is_int($value) && !is_string($value)) {
            return false;
        }

        // Filter the value
        $result = filter_var($value, FILTER_VALIDATE_FLOAT);
        if ($result === false) {
            return false;
        }

        // Set the value
        $value = $result;
        return true;
    }
}
