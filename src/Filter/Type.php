<?php

declare(strict_types=1);

namespace Nucleus\Filter;

/**
 * Represents a type.
 */
interface Type
{
    /**
     * Filters a value.
     *
     * @param mixed $value The value.
     * @return mixed The filtered value.
     */
    public function filter($value);
}
