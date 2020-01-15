<?php

declare(strict_types=1);

namespace Nucleus\Types\Types;

use Nucleus\Types\Exceptions\InvalidValueException;
use Nucleus\Types\Type;

/**
 * Represents the integer base type.
 */
class IntegerType implements Type
{
    /**
     * The singleton instance of this class.
     *
     * @var IntegerType
     */
    private static $instance;

    /**
     * Returns the singleton instance of the integer base type.
     *
     * @return IntegerType The integer base type.
     */
    public static function get(): IntegerType
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

        // Only accept integers and strings
        if (!is_int($value) && !is_string($value)) {
            throw new InvalidValueException("Invalid integer.");
        }

        // Filter the value
        $result = filter_var($value, FILTER_VALIDATE_INT);
        if ($result === false) {
            throw new InvalidValueException("Invalid integer.");
        }

        // Return the filtered value
        return $result;
    }
}
