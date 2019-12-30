<?php

declare(strict_types=1);

namespace Nucleus\Compil;

/**
 * Class used to interpret a string based on a parse tree.
 */
class Parser
{
    /**
     * The parse tree.
     *
     * @var ParseNode
     */
    private $parseTree;

    /**
     * The tokenizer.
     *
     * @var Tokenizer
     */
    private $tokenizer;

    /**
     * The input string.
     *
     * @var string
     */
    private $str;

    /**
     * The string's tokens.
     *
     * @var array
     */
    private $tokens;

    /**
     * The current token position.
     *
     * @var int
     */
    private $index;

    /**
     * Initializes the parser.
     *
     * @param Language $language The parser language.
     */
    public function __construct(Language $language)
    {
        $this->parseTree = $language->parseTree();
        $this->tokenizer = new Tokenizer($language);
    }

    /**
     * Takes a string and produces a syntax tree.
     *
     * @param string $str The input string.
     * @return SyntaxNode|null The root node of the syntax tree, or null if the
     * string couldn't be parsed.
     */
    public function parse(string $str): ?SyntaxNode
    {
        $this->str    = $str;
        $this->index  = 0;
        $this->tokens = $this->tokenizer->tokenize($str);

        $syntaxTree = new SyntaxNode();
        $success    = $this->matchNode($this->parseTree, $syntaxTree);

        $syntaxTree->setValue($str);
        return $success ? $syntaxTree->child($this->parseTree->name()) : null;
    }

    /**
     * Tries to match a parse node.
     *
     * @param ParseNode $node The parse node.
     * @param SyntaxNode $parent The parent syntax tree node.
     * @param boolean $fullMatch If true, indicates that the parse node must
     * match all tokens.
     * @return boolean True if the tokens match the parse node, false
     * otherwise.
     */
    private function matchNode(
        ParseNode $node,
        SyntaxNode $parent,
        bool $fullMatch = true
    ): bool {
        // Check if the index is valid. If the pattern can match an empty
        // string (i.e. has an empty pattern), this is a match.
        $nbTokens = count($this->tokens);
        if ($this->index >= $nbTokens) {
            return self::matchesEmptyStrings($node);
        }

        // Store the start index and token
        $startIndex = $this->index;
        $startToken = $this->tokens[$this->index];

        // Try to match each pattern of the node.
        foreach ($node->patterns() as $pattern) {
            // Initialize the child syntax node.
            $child = new SyntaxNode();

            if ($this->matchPattern($pattern, $child)) {
                // If a full match is requested, check that all
                // tokens were consumed.
                if ($fullMatch && $this->index < $nbTokens) {
                    $parent->clear();
                    $this->index = $startIndex;
                    continue;
                }

                // Add the child syntax node.
                $endToken = $this->tokens[$this->index - 1];
                $value    = $this->getValue($startToken, $endToken);

                $child->setValue($value);
                $child->setPattern($pattern->name());

                $parent->setChild($node->name(), $child);
                return true;
            }
        }

        // No pattern matched.
        return false;
    }

    /**
     * Tries to match a parse node pattern.
     *
     * @param Pattern $pattern The pattern.
     * @param SyntaxNode $parent The parent syntax tree node.
     * @return boolean True if the pattern could be matched, false otherwise.
     */
    private function matchPattern(Pattern $pattern, SyntaxNode $parent): bool
    {
        // Check if the index is valid.
        if ($this->index >= count($this->tokens)) {
            return false;
        }

        // Keep the previous idex. This wil be useful to 'restore' the consumed
        // tokens in case the pattern didn't match.
        $startIndex = $this->index;

        // Try to match the pattern's items.
        foreach ($pattern->items() as $item) {
            // Item is a string, try to match it with the current token.
            if (is_string($item) && $this->matchCurrentToken($item)) {
                continue;
            }

            // Node pattern item.
            $isNode = is_subclass_of($item, ParseNode::class);
            if ($isNode && $this->matchNode($item, $parent, false)) {
                continue;
            }

            // Item didn't match.
            $this->index = $startIndex;
            return false;
        }

        return true;
    }

    /**
     * Tries to match the current token with the given regular expression.
     * Consumes the current token if there is a match.
     *
     * @param string $string The string.
     * @return boolean True if the token matched, false otherwise.
     */
    private function matchCurrentToken(string $regex): bool
    {
        // Empty regex: always matches without consuming tokens.
        if (empty($regex)) {
            return true;
        }

        // Non-empty regex: try to match the current token.
        $tokenValue = $this->tokens[$this->index]->value();
        $regex      = '/^' . trim($regex, '/') . '$/';

        // If there is a match, consume the token.
        if (preg_match($regex, $tokenValue)) {
            $this->index += 1;
            return true;
        }

        // No match.
        return false;
    }

    /**
     * Returns the string between two tokens.
     *
     * @param Token $startToken The start token.
     * @param Token $endToken The end token.
     * @return string The string value.
     */
    private function getValue(Token $startToken, Token $endToken): string
    {
        $start  = $startToken->startIndex();
        $length = $endToken->endIndex() - $start;
        return substr($this->str, $start, $length);
    }

    /**
     * Returns whether a parse node matches an empty string. A parse node
     * matches empty strings if and only if it has a pattern whose only item is
     * an empty string.
     *
     * @param ParseNode $parseNode The parse node.
     * @return boolean True if the node matches empty strings, false otherwise.
     */
    private static function matchesEmptyStrings(ParseNode $parseNode): bool
    {
        foreach (array_values($parseNode->patterns()) as $pattern) {
            $items = $pattern->items();
            if (count($items) === 1 && $items[0] === '') {
                return true;
            }
        }

        return false;
    }
}
