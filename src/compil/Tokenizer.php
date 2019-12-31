<?php

declare(strict_types=1);

namespace Nucleus\Compil;

/**
 * Class used to decompose a string into tokens, i.e. strings units usable by
 * a parser.
 */
class Tokenizer
{
    /**
     * The string delimiters.
     *
     * @var array
     */
    private $stringDelimiters;

    /**
     * A regex matching all string ending with a special token.
     *
     * @var string
     */
    private $specialTokensPattern;

    /**
     * The input string.
     *
     * @var string
     */
    private $str;

    /**
     * The current index in the input string.
     *
     * @var int
     */
    private $index;

    /**
     * Initializes the tokenizer.
     *
     * @param Language $language The language to use.
     */
    public function __construct(Language $language)
    {
        $this->str   = '';
        $this->index = 0;

        // String delimiters
        $this->stringDelimiters =
            $language->stringDelimiters();

        // Special tokens
        $this->specialTokensPattern =
            self::multiPattern($language->specialTokens(), false, true);
    }

    /**
     * Tokenizes a string.
     *
     * @param string $str The input string.
     * @return array The array of tokens.
     */
    public function tokenize(string $str): array
    {
        $this->str   = $str;
        $this->index = 0;

        $tokens = [];
        while ($this->index < strlen($str)) {
            $token = new Token($this->index);

            // Try to tokenize a white space token
            $token->clear();
            if ($this->tokenizeWhiteSpace($token)) {
                continue;
            }

            // Try to tokenize a string
            $token->clear();
            if ($this->tokenizeString($token)) {
                $tokens[] = $token;
                continue;
            }

            // Try to tokenize a word
            $token->clear();
            if ($this->tokenizeWord($token)) {
                $tokens[] = $token;
                continue;
            }

            // Unable to tokenize the remaining characters. Exit.
            return [];
        }

        return $tokens;
    }

    /**
     * Tokenizes white spaces.
     *
     * @param Token $token The token.
     * @return boolean True if the tokenization was successful, false
     * otherwise.
     */
    public function tokenizeWhiteSpace(Token $token): bool
    {
        while ($this->index < strlen($this->str)) {
            // Non-space character: if the token is empty, the tokenization
            // failed, otherwize it succeeded.
            if (!preg_match('/\s/', $this->str[$this->index])) {
                return !$token->isEmpty();
            }

            // Add space to token and go to the next character.
            $token->append($this->str[$this->index]);
            $this->index += 1;
        }

        return true;
    }

    /**
     * Tokenizes white a string.
     *
     * @param Token $token The token.
     * @return boolean True if the tokenization was successful, false
     * otherwise.
     */
    public function tokenizeString(Token $token): bool
    {
        $escape = false;
        while ($this->index < strlen($this->str)) {
            // Get the current character.
            $currentChar = $this->str[$this->index];

            // Check if the token is a string
            if (
                $token->isEmpty() &&
                !in_array($currentChar, $this->stringDelimiters)
            ) {
                return false;
            }

            // Add the current character to the token.
            $token->append($currentChar);
            $this->index += 1;

            // Determine if the end of string is reached
            if (
                strlen($token->value()) > 1 &&
                !$escape && $token->value()[-1] === $token->value()[0]
            ) {
                return true;
            }

            $escape = !$escape && $currentChar === '\\';
        }

        return false;
    }

    /**
     * Tokenizes white a word (i.e. a sequence of character ended by a white
     * space).
     *
     * @param Token $token The token.
     * @return boolean True if the tokenization was successful, false
     * otherwise.
     */
    public function tokenizeWord(Token $token): bool
    {
        while ($this->index < strlen($this->str)) {
            // Space character reached
            if (preg_match('/\s/', $this->str[$this->index])) {
                return true;
            }

            // Add character to token
            $token->append($this->str[$this->index]);
            $this->index += 1;

            // Check if the token ends with a special token
            $matches = [];
            if (preg_match(
                $this->specialTokensPattern,
                $token->value(),
                $matches
            )) {
                $matchLength = strlen($matches[0]);
                $isFullMatch = $matchLength == $token->length();

                if (!$isFullMatch) {
                    $index -= $matchLength;
                    $token = $token->rtrim($matchLength);
                }

                return true;
            }
        }

        return true;
    }

    /**
     * Returns a pattern matching a list of strings.
     *
     * @param array $stringsToMatch The strings.
     * @param boolean $startsWith If true, the resulting pattern will match
     * starting with the given strings.
     * @param boolean $endsWith If true, the resulting pattern will match
     * ending with the given strings.
     * @return string The pattern,
     */
    private static function multiPattern(
        array $stringsToMatch,
        bool $startsWith = false,
        bool $endsWith = false
    ): string {
        $multipattern = '';

        foreach ($stringsToMatch as $curString) {
            $multipattern .= empty($multipattern) ? '' : '|';
            $multipattern .= $startsWith ? '^' : '';
            $multipattern .= preg_quote($curString);
            $multipattern .= $endsWith ? '$' : '';
        }

        return "/$multipattern/";
    }
}
