<?php

declare(strict_types=1);

namespace Nucleus\Filter\Types;

use Nucleus\Filter\Exceptions\InvalidValueException;
use Nucleus\Filter\Type;

/**
 * Represents the string base type.
 */
class StringType implements Type
{
    /**
     * {@inheritDoc}
     */
    public function filter($value)
    {
        // Special case: null
        if ($value === null) {
            throw new InvalidValueException('string', $value);
        }

        // Special case: booleans
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        // Filter the value
        $result = filter_var($value, FILTER_SANITIZE_STRING);
        if ($result === false) {
            throw new InvalidValueException('string', $value);
        }

        // Return the filtered value
        return $result;
    }
}
