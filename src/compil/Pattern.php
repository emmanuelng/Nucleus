<?php

declare(strict_types=1);

namespace Nucleus\Compil;

/**
 * Represents a token pattern.
 * A pattern has items that correspond to either one (regex) or many (parse
 * node) tokens.
 */
class Pattern
{
    /**
     * The pattern name.
     *
     * @var string
     */
    private $name;

    /**
     * The pattern items.
     *
     * @var array
     */
    private $items;

    /**
     * Initializes the pattern.
     *
     * @param string $name The pattern name.
     * @param array $items The pattern items.
     */
    public function __construct(string $name, array $items)
    {
        $this->name  = $name;
        $this->items = $items;
    }

    /**
     * Returns the pattern name.
     *
     * @return string The name.
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Returns the pattern items.
     *
     * @return array The items.
     */
    public function items(): array
    {
        return $this->items;
    }
}
