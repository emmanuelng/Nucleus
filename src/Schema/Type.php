<?php

declare(strict_types=1);

namespace Nucleus\Schema;

/**
 * Represents a type.
 */
interface Type
{
    /**
     * Filters a value. Throws an exception if the value isn't valid.
     *
     * @param mixed $value The value to filter
     * @return mixed the filtered value.
     */
    public function filter($value);
}