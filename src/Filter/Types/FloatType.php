<?php

declare(strict_types=1);

namespace Nucleus\Filter\Types;

use Nucleus\Filter\Exceptions\InvalidValueException;
use Nucleus\Filter\Type;

/**
 * Represents the floating point base type.
 */
class FloatType implements Type
{
    /**
     * {@inheritDoc}
     */
    public function filter($value)
    {
        // Only accept floats, integers and strings
        if (!is_float($value) && !is_int($value) && !is_string($value)) {
            throw new InvalidValueException('float', $value);
        }

        // Filter the value
        $result = filter_var($value, FILTER_VALIDATE_FLOAT);
        if ($result === false) {
            throw new InvalidValueException('float', $value);
        }

        // Return the filtered value
        return $result;
    }
}
