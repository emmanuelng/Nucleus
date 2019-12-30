<?php

declare(strict_types=1);

namespace Nucleus\Compil;

/**
 * Represents a node of a syntax tree.
 */
class SyntaxNode
{
    /**
     * The node's children nodes.
     *
     * @var array
     */
    private $children;

    /**
     * The node's value.
     *
     * @var string
     */
    private $value;

    /**
     * The matched pattern name.
     *
     * @var string
     */
    private $pattern;

    /**
     * Initializes the syntax node.
     */
    public function __construct()
    {
        $this->children = [];
        $this->value    = '';
        $this->pattern  = '';
    }

    /**
     * Allows to access the node children as class properties.
     *
     * @param string $name The child name.
     * @return SyntaxNode|null The child node or null if it doesn't exist.
     */
    public function __get(string $name): ?SyntaxNode
    {
        return $this->child($name);
    }

    /**
     * Sets a child of this node.
     *
     * @param string $name The child's name.
     * @param SyntaxNode $child The child.
     * @return void
     */
    public function setChild(string $name, SyntaxNode $child): void
    {
        $this->children[$name] = $child;
    }

    /**
     * Sets the node's value.
     *
     * @param string $value The value.
     * @return void
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * Set the matched pattern.
     *
     * @param string $pattern The pattern name.
     * @return void
     */
    public function setPattern(string $pattern): void
    {
        $this->pattern = $pattern;
    }

    /**
     * Returns a child.
     *
     * @param string $name The child name
     * @return SyntaxNode|null The child node or null if it doesn't exist.
     */
    public function child(string $name): ?SyntaxNode
    {
        return $this->children[$name] ?? null;
    }

    /**
     * Returns the node's value.
     *
     * @return string The value.
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Returns the matched pattern name.
     *
     * @return string The pattern name.
     */
    public function pattern(): string
    {
        return $this->pattern;
    }

    /**
     * Clears the node.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->children = [];
        $this->value    = '';
        $this->pattern  = '';
    }
}
