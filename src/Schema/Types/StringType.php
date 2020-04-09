<?php

declare(strict_types=1);

namespace Nucleus\Schema\Types;

use Nucleus\Schema\Exceptions\InvalidValueException;
use Nucleus\Schema\Type;

/**
 * String base type.
 */
class StringType implements Type
{
    /**
     * {@inheritDoc}
     */
    public function filter($value)
    {
        // Special case: booleans
        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }

        // Filter the value
        $result = filter_var($value, FILTER_SANITIZE_STRING);
        if ($result === false) {
            throw new InvalidValueException("Invalid string.");
        }

        // Set the value
        return $result;
    }
}
