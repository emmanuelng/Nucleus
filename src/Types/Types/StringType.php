<?php

declare(strict_types=1);

namespace Nucleus\Types\Types;

use Nucleus\Types\Exceptions\InvalidValueException;
use Nucleus\Types\Type;

/**
 * Represents the string base type.
 */
class StringType implements Type
{
    /**
     * The singleton instance of this class.
     *
     * @var StringType
     */
    private static $instance;

    /**
     * Returns the singleton instance of the string base type.
     *
     * @return StringType The string base type.
     */
    public static function get(): StringType
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
        // Special case: null
        if ($value === null) {
            return null;
        }

        // Special case: booleans
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        // Filter the value
        $result = filter_var($value, FILTER_SANITIZE_STRING);
        if ($result === false) {
            throw new InvalidValueException("Invalid string.");
        }

        // Return the filtered value
        return $result;
    }
}
