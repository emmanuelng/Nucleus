<?php

declare(strict_types=1);

namespace Nucleus\Compil\Languages;

use Nucleus\Compil\Exceptions\InvalidLanguageException;
use Nucleus\Compil\Language;
use Nucleus\Compil\ParseNode;
use Nucleus\Compil\Pattern;

/**
 * A concrete class of the Language interface, allowing to define languages
 * with arrays.
 *
 * Usage example:
 * ```
 *     $lang = new ArrayLanguage([
 *        'stringDelimiters' => ['"', '\''],
 *        'specialTokens' => ['(', ')', '=='],
 *        'parseTree' => [
 *            'root' => [
 *                'first'  => ['child'],
 *                'second' => ['/b/', 'root'],
 *                'empty'  => ['']
 *            ],
 *            'child' => [
 *                'test' => ['/test/']
 *            ]
 *        ]
 *    ]);
 * ```
 */
class ArrayLanguage implements Language
{
    /**
     * List of string delimiters.
     *
     * @var array
     */
    private $stringDelimiters;

    /**
     * List of special tokens.
     *
     * @var array
     */
    private $specialTokens;

    /**
     * The parse tree (more precisely, the root of the parse tree).
     *
     * @var ParseNode
     */
    private $parseTree;

    /**
     * A map of all parse nodes defined in the array used to initilize the
     * language.
     *
     * @var array
     */
    private $parseNodes;

    /**
     * Initializes the language with an array.
     *
     * @param array $language The array describing the language.
     */
    public function __construct(array $language)
    {
        // String delimiters
        $this->stringDelimiters = $language['stringDelimiters'] ?? [];
        if (!is_array($this->stringDelimiters)) {
            $msg = 'String delimiters must be an array.';
            throw new InvalidLanguageException($msg);
        }

        // Special tokens
        $this->specialTokens = $language['specialTokens'] ?? [];
        if (!is_array($this->specialTokens)) {
            $msg = 'Special tokens must be an array.';
            throw new InvalidLanguageException($msg);
        }

        // Parse tree
        $parseTreeArr = $language['parseTree'] ?? [];
        if (!is_array($parseTreeArr) || empty($parseTreeArr)) {
            $msg = 'Parse tree must be an array and must not be empty.';
            throw new InvalidLanguageException($msg);
        }

        // Build the parse tree nodes.
        $this->buildParseNodes($parseTreeArr);

        // Get the parse tree root (first element in the parse tree array).
        $nodeNames       = array_keys($parseTreeArr);
        $rootNodeName    = array_shift($nodeNames);
        $this->parseTree = $this->parseNodes[$rootNodeName];
    }

    /**
     * {@inheritDoc}
     */
    public function stringDelimiters(): array
    {
        return $this->stringDelimiters;
    }

    /**
     * {@inheritDoc}
     */
    public function specialTokens(): array
    {
        return $this->specialTokens;
    }

    /**
     * {@inheritDoc}
     */
    public function parseTree(): ParseNode
    {
        return $this->parseTree;
    }

    /**
     * Builds the parse nodes.
     *
     * @param array $parseTreeArr The parse tree array.
     * @return void
     */
    private function buildParseNodes(array $parseTreeArr): void
    {
        // Build a map of parse nodes. Don't initilize their patterns yet. This
        // allows to have recursive patterns.
        foreach (array_keys($parseTreeArr) as $name) {
            $this->parseNodes[$name] = new class ($name) implements ParseNode
            {
                /**
                 * The node name.
                 *
                 * @var string
                 */
                private $name;

                /**
                 * The node patterns.
                 *
                 * @var array
                 */
                private $patterns;

                /**
                 * Initializes the parse node.
                 *
                 * @param string $name Name of the node.
                 */
                public function __construct(string $name)
                {
                    $this->name     = $name;
                    $this->patterns = [];
                }

                /**
                 * {@inheritDoc}
                 */
                public function name(): string
                {
                    return $this->name;
                }

                /**
                 * {@inheritDoc}
                 */
                public function patterns(): array
                {
                    return $this->patterns;
                }

                /**
                 * Sets the patterns of the node.
                 *
                 * @param array $patterns The array of patterns.
                 * @return void
                 */
                public function setPatterns(array $patterns): void
                {
                    $this->patterns = $patterns;
                }
            };
        }

        // Now that all nodes were created, build their patterns.
        $this->buildParseNodePatterns($parseTreeArr);
    }

    /**
     * Initilizes the pattern of each parse node.
     *
     * @param array $parseTreeArr The parse tree array.
     * @return void
     */
    private function buildParseNodePatterns(array $parseTreeArr)
    {
        foreach ($parseTreeArr as $node => $patternsArr) {
            $patterns = [];
            foreach ($patternsArr as $name => $items) {
                $patterns[$name] = $this->buildPattern($name, $items);
            }

            $this->parseNodes[$node]->setPatterns($patterns);
        }
    }

    /**
     * Builds a pattern.
     *
     * @param string $name The pattern name.
     * @param array $itemsArr The pattern items.
     * @return Pattern The pattern object.
     */
    private function buildPattern(string $name, array $itemsArr): Pattern
    {
        $items = [];

        foreach ($itemsArr as $item) {
            if (!is_string($item)) {
                $msg = "Invalid pattern item $item.";
                throw new InvalidLanguageException($msg);
            }

            if (empty($item)) {
                $items[] = $item;
                continue;
            }

            if ($item[0] === '/' && $item[-1] === '/') {
                $items[] = $item;
                continue;
            }

            if (array_key_exists($item, $this->parseNodes)) {
                $items[] = $this->parseNodes[$item];
                continue;
            }

            $msg = "Undefined parse node $item.";
            throw new InvalidLanguageException($msg);
        }

        return new Pattern($name, $items);
    }
}
