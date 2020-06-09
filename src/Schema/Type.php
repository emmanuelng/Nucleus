<?php

declare(strict_types=1);

namespace Nucleus\Schema;

use Nucleus\Schema\Exceptions\InvalidValueException;

/**
 * Represents a type.
 */
interface Type
{
    /**
     * Filters a value. Throws an exception if the value isn't valid.
     *
     * @param mixed $value The value to filter
     *
     * @return mixed the filtered value.
     *
     * @throws InvalidValueException If the value is not compatible with this
     *                               type.
     */
    public function filter($value);
}