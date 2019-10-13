<?php

declare(strict_types=1);

namespace Nucleus\Filter\Types;

use Nucleus\Filter\Exceptions\InvalidValueException;
use Nucleus\Filter\Type;

/**
 * ### IntegerType class
 *
 * Represents the integer base type.
 */
class IntegerType implements Type
{
    public function filter($value)
    {
        // Only accept integers and strings
        if (!is_int($value) && !is_string($value)) {
            throw new InvalidValueException('int', $value);
        }

        // Filter the value
        $result = filter_var($value, FILTER_VALIDATE_INT);
        if ($result === false) {
            throw new InvalidValueException('int', $value);
        }

        // Return the filtered value
        return $result;
    }
}
