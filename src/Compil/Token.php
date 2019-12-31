<?php

declare(strict_types=1);

namespace Nucleus\Compil;

/**
 * Represents a token.
 */
class Token
{
    /**
     * The token value.
     *
     * @var string
     */
    private $value;

    /**
     * The token position in the original string.
     *
     * @var int
     */
    private $index;

    /**
     * Initializes the token.
     *
     * @param integer $index The token position.
     */
    public function __construct(int $index)
    {
        $this->value = '';
        $this->index = $index;
    }

    /**
     * Clears the token value.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->value = '';
    }

    /**
     * Appends a string to the token.
     *
     * @param string $str The string to append.
     * @return void
     */
    public function append(string $str): void
    {
        $this->value .= $str;
    }

    /**
     * Returns the token value.
     *
     * @return string The value.
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Returns the start position of the token.
     *
     * @return integer The start position.
     */
    public function startIndex(): int
    {
        return $this->index;
    }

    /**
     * Returns the end position of the token.
     *
     * @return integer The end position.
     */
    public function endIndex(): int
    {
        return $this->index + $this->length();
    }

    /**
     * Returns whether the token is empty.
     *
     * @return boolean True if the token is empty, false otherwise.
     */
    public function isEmpty(): bool
    {
        return empty($this->value);
    }

    /**
     * Returns the token length.
     *
     * @return integer The length.
     */
    public function length(): int
    {
        return strlen($this->value);
    }

    /**
     * Removes a given number of characters at the right of the token.
     *
     * @param integer $nbChars The number of characters to remove.
     * @return void
     */
    public function rtrim(int $nbChars): void
    {
        if ($nbChars > 0) {
            $this->value = substr($this->value, 0, -$nbChars);
            $this->index = max($this->index, $this->index - $nbChars);
        }
    }
}
