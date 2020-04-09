<?php

declare(strict_types=1);

namespace Nucleus\Schema\Types;

use Nucleus\Schema\Exceptions\InvalidValueException;
use Nucleus\Schema\Type;

/**
 * Boolean base type.
 */
class BooleanType implements Type
{
    /**
     * {@inheritDoc}
     */
    public function filter($value)
    {
        // Booleans: Accept the value
        if (is_bool($value)) {
            return $value;
        }

        // Strings: Allow variations of 'true' and 'false'
        if (is_string($value)) {
            $valueLower = strtolower($value);
            if ($valueLower === 'true') {
                return true;
            } else if ($valueLower === 'false') {
                return false;
            }
        }

        // Invalid value
        throw new InvalidValueException("Invalid boolean.");
    }
}
