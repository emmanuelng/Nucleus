<?php

declare(strict_types=1);

namespace Nucleus\Neon;

use Nucleus\Compil\Languages\ArrayLanguage;

/**
 * Represents the NEON (NuclEus Object Notation) notation.
 * This notation is a custom JSON notation for Nucleus objects.
 */
class NeonLanguage extends ArrayLanguage
{
    /**
     * The NEON language definition.
     *
     * @var array
     */
    private static $definition =
    [
        'stringDelimiters' => ['"', '\''],
        'specialTokens'    => ['{', '}', '=', '[]'],

        'parseTree' => [
            'root' => [
                'route'  => ['route'],
                'schema' => ['schema']
            ],

            'route' => [
                'route' => ['/route/', 'identifier', '/{/', 'routeBody', '/}/']
            ],
            'routeBody' => [
                'method'     => ['/method/', 'string', 'routeBody'],
                'url'        => ['/url/', 'string', 'routeBody'],
                'parameters' => ['/parameters/', '/{/', 'schemaBody', '/}/', 'routeBody'],
                'request'    => ['/request/', '/{/', 'schemaBody', '/}/', 'routeBody'],
                'response'   => ['/response/', '/{/', 'schemaBody', '/}/', 'routeBody'],
                'callback'   => ['/callback/', 'callable', 'routeBody'],
                'empty'      => ['']
            ],

            'schema' => [
                'schema' => ['/schema/', 'identifier', '/{/', 'schemaBody', '/}/']
            ],
            'schemaBody' => [
                'field' => ['schemaField', 'schemaBody'],
                'empty' => ['']
            ],
            'schemaField' => [
                'optional'  => ['type', 'identifier', '/=/', 'value', 'schemaFieldDoc'],
                'mandatory' => ['type', 'identifier', 'schemaFieldDoc'],
            ],
            'schemaFieldDoc' => [
                'doc'   => ['/:/', 'string'],
                'empty' => ['']
            ],

            'identifier' => [
                'identifier' => ['/[a-zA-Z_][a-zA-Z0-9]+/']
            ],
            'callable' => [
                'callable' => ['/(.)+/']
            ],
            'type' => [
                'list'   => ['/[a-zA-Z_][a-zA-Z0-9]+/', '/\[\]/'],
                'simple' => ['/[a-zA-Z_][a-zA-Z0-9]+/']
            ],
            'value' => [
                'value' => ['/(.)+/']
            ],
            'string' => [
                'doubleQuotes' => ['/\"((.*)(\s)?)*\"/'],
                'singleQuotes' => ['/\'((.*)(\s)?)*\'/']
            ]
        ]
    ];

    /**
     * Initializes the language.
     */
    public function __construct()
    {
        parent::__construct(self::$definition);
    }
}
