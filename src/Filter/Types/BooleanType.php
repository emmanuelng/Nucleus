<?php

declare(strict_types=1);

namespace Nucleus\Filter\Types;

use Nucleus\Filter\Exceptions\InvalidValueException;
use Nucleus\Filter\Type;

/**
 * Represents the boolean base type.
 */
class BooleanType implements Type
{
    /**
     * {@inheritDoc}
     */
    public function filter($value)
    {
        // Strings: Allow variations of 'true' and 'false'
        if (is_string($value)) {
            $valueLower = strtolower($value);
            if ($valueLower === 'true') {
                return true;
            } else if ($valueLower === 'false') {
                return false;
            }
        }

        // Booleans: Return the value directly
        if (is_bool($value)) {
            return $value;
        }

        // Invalid value
        throw new InvalidValueException('bool', $value);
    }
}
