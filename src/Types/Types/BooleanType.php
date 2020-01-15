<?php

declare(strict_types=1);

namespace Nucleus\Types\Types;

use Nucleus\Types\Exceptions\InvalidValueException;
use Nucleus\Types\Type;

/**
 * Represents the boolean base type.
 */
class BooleanType implements Type
{
    /**
     * The singleton instance of this class.
     *
     * @var BooleanType
     */
    private static $instance;

    /**
     * Returns the singleton instance of the boolean base type.
     *
     * @return BooleanType The boolean base type.
     */
    public static function get(): BooleanType
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Initializes the type.
     */
    private function __construct()
    {
        // Nothing to do...
    }

    /**
     * {@inheritDoc}
     */
    public function filter($value)
    {
        // Accept null values.
        if ($value === null) {
            return null;
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

        // Booleans: Return the value directly
        if (is_bool($value)) {
            return $value;
        }

        // Invalid value
        throw new InvalidValueException("Invalid boolean.");
    }
}
