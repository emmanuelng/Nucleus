<?php

declare(strict_types=1);

namespace Nucleus\Compil;

/**
 * Represents a language.
 */
interface Language
{
    /**
     * Returns the list of characters that delimit strings.
     * Typically " or '.
     *
     * @return array The list of string delimiters.
     */
    public function stringDelimiters(): array;

    /**
     * Returns a list of special tokens, i.e. strings that must be tokenized,
     * even if they are not surrounded by spaces. For example, parentheses
     * must be tokenized, even if they are surrounded by characters.
     *
     * @return array The list of special tokens.
     */
    public function specialTokens(): array;

    /**
     * Returns the root node of the language's parse tree. This node must
     * match each string defined as valid by the language.
     *
     * @return ParseNode The root node.
     */
    public function parseTree(): ParseNode;
}
