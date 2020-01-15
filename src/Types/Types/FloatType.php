<?php

declare(strict_types=1);

namespace Nucleus\Types\Types;

use Nucleus\Types\Exceptions\InvalidValueException;
use Nucleus\Types\Type;

/**
 * Represents the floating point base type.
 */
class FloatType implements Type
{
    /**
     * The singleton instance of this class.
     *
     * @var FloatType
     */
    private static $instance;

    /**
     * Returns the singleton instance of the float base type.
     *
     * @return FloatType The float base type.
     */
    public static function get(): FloatType
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

        // Only accept floats, integers and strings
        if (!is_float($value) && !is_int($value) && !is_string($value)) {
            throw new InvalidValueException("Invalid float");
        }

        // Filter the value
        $result = filter_var($value, FILTER_VALIDATE_FLOAT);
        if ($result === false) {
            throw new InvalidValueException("Invalid float");
        }

        // Return the filtered value
        return $result;
    }
}
