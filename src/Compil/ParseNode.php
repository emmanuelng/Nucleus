<?php

declare(strict_types=1);

namespace Nucleus\Compil;

/**
 * Represents a node of a parse tree.
 */
interface ParseNode
{
    /**
     * Returns the name of the node.
     *
     * @return string The name.
     */
    public function name(): string;

    /**
     * Returns the patterns corresponding to the node.
     *
     * @return array An array of pattern objects.
     */
    public function patterns(): array;
}
