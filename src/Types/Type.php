<?php

declare(strict_types=1);

namespace Nucleus\Types;

/**
 * Represents a type.
 */
interface Type
{
    /**
     * Validates and casts a value. This method must throw an exception if the
     * input value isn't valid.
     *
     * @param mixed $value
     * @return mixed The filtered value.
     */
    public function filter($value);
}
