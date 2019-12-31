<?php

declare(strict_types=1);

namespace Nucleus\Neon;

use Exception;
use Nucleus\Compil\Parser;
use Nucleus\Compil\SyntaxNode;
use Nucleus\Neon\Exceptions\CompilationErrorException;
use Nucleus\Router\Routes\ArrayRoute;

/**
 * Class used to compile NEON files.
 */
class NeonCompiler
{
    /**
     * The NEON parser.
     *
     * @var Parser
     */
    private static $parser;

    /**
     * Compiles all .neon files in the given path.
     *
     * @param string $path A comma-separated list of directories.
     * @return array An array of routes.
     */
    public static function compile(string $path): array
    {
        $routes  = [];
        $schemas = [];

        // Compile all neon files in the path.
        foreach (self::getFiles($path) as $file) {
            // Try to get a route
            $name  = '';
            $route = self::compileRoute($file, $name);

            if ($route != null) {
                // Check that the route isn't already defined.
                if (isset($routes[$name])) {
                    $msg = "Duplicate route name $name";
                    throw new CompilationErrorException($msg);
                }

                // Add the route.
                $routes[$name] = $route;
                continue;
            }

            // Try to get a schema
            $name   = '';
            $schema = self::compileSchema($file, $name);

            if ($schema != null) {
                // Check that the schema isn't already defined.
                if (isset($schemas[$name])) {
                    $msg = "Duplicate schema name $name";
                    throw new CompilationErrorException($msg);
                }

                // Add the schema.
                $schemas[$name] = $schema;
                continue;
            }
        }

        // Link the routes with the schemas and instanciate them.
        $routeObjs = [];
        foreach (NeonLinker::link($routes, $schemas) as $route) {
            $routeObjs[] = new ArrayRoute($route);
        }

        // Return the list of routes.
        return $routeObjs;
    }

    /**
     * Returns the list of all .neon files in the given path and their
     * sub-directories.
     *
     * @param string $path A comma-separated list of directories.
     * @return array The list of files.
     */
    private static function getFiles(string $path): array
    {
        if (empty($path)) {
            return [];
        }

        $files = [];
        foreach (explode(',', $path) as $directory) {
            $directory = trim($directory, '\\/');
            $subDirLst = glob("$directory\*", GLOB_ONLYDIR);
            $subPath   = implode(',', $subDirLst);

            $files = array_merge($files, glob("$directory\*.neon"));
            $files = array_merge($files, self::getFiles($subPath));
        }

        return $files;
    }

    /**
     * Compiles a route defined in a .neon file.
     * *Warning: This method doesn't link the route with the referenced schemas.
     * To perform a link, use the `compile()` method.*
     *
     * @param string $filename The file name.
     * @param string $name The route name. Is set by reference.
     * @return array|null An array containing the route's information or null
     * if the file doesn't define a route.
     */
    private static function compileRoute(
        string $filename,
        string &$name
    ): ?array {
        // Initialize the parser if necessary
        if (!isset(self::$parser)) {
            self::$parser = new Parser(new NeonLanguage);
        }

        // Parse the file
        $fileContents = file_get_contents($filename);
        $syntaxTree   = self::$parser->parse($fileContents);

        // The file either has syntax errors.
        if ($syntaxTree == null) {
            $msg = "Compilation error in file $filename: Couldn't parse file.";
            throw new CompilationErrorException($msg);
        }

        // The file is syntactically correct, but doesn't define a route.
        if ($syntaxTree->route == null) {
            return null;
        }

        // Generate the route.
        try {
            $route = [];
            self::generateRoute($syntaxTree->route, $name, $route);
            return $route;
        } catch (Exception $e) {
            $msg = "Compilation error in file $filename: " . $e->getMessage();
            throw new CompilationErrorException($msg);
        }
    }

    /**
     * Compiles a schema defined in a .neon file.
     *
     * @param string $filename The file name.
     * @param string $name The schema name. Is set by reference.
     * @return array|null An array containing the schema's fields or null if
     * the file doesn't define a schema.
     */
    private static function compileSchema(
        string $filename,
        string &$name
    ): ?array {
        // Initialize the parser if necessary
        if (!isset(self::$parser)) {
            self::$parser = new Parser(new NeonLanguage);
        }

        // Parse the file
        $fileContents = file_get_contents($filename);
        $syntaxTree   = self::$parser->parse($fileContents);

        // The file either has syntax errors.
        if ($syntaxTree == null) {
            $msg = "Compilation error in file $filename: Syntax error.";
            throw new CompilationErrorException($msg);
        }

        // The file is syntactically correct, but doesn't define a schema.
        if ($syntaxTree->schema == null) {
            return null;
        }

        // Generate the schema.
        try {
            $schema = [];
            self::generateSchema($syntaxTree->schema, $name, $schema);
        } catch (Exception $e) {
            $msg = "Compilation error in file $filename: " . $e->getMessage();
            throw new CompilationErrorException($msg);
        }

        // Return the schema.
        return $schema;
    }

    /**
     * Fills an array with a schema's information.
     *
     * @param SyntaxNode $node The syntax node corresponding to the schema.
     * @param string $name The schema name. Is set by reference.
     * @param array $result The array to fill.
     * @return void
     */
    private static function generateSchema(
        SyntaxNode $node,
        string &$name,
        array &$result
    ): void {
        $name = $node->identifier->value();
        self::generateSchemaBody($node->schemaBody, $result);
    }

    /**
     * Fills an array with a route's information.
     *
     * @param SyntaxNode $node The syntax node corresponding to the route.
     * @param string $name The schema name. Is set by reference.
     * @param array $result The array to fill.
     * @return void
     */
    private static function generateRoute(
        SyntaxNode $node,
        string &$name,
        array &$result
    ): void {
        $name = $node->identifier->value();
        self::generateRouteBody($node->routeBody, $result);
    }

    /**
     * Fills an array with the informations contained in a route's body.
     *
     * @param SyntaxNode $node The syntax node corresponding to the route's
     * body.
     * @param array $result The array to fill.
     * @return void
     */
    private static function generateRouteBody(
        SyntaxNode $node,
        array &$result
    ): void {
        $key   = '';
        $value = null;

        switch ($node->pattern()) {
            case 'method':
                $key   = 'method';
                $value = trim($node->string->value(), '"\'');
                break;

            case 'url':
                $key   = 'url';
                $value = trim($node->string->value(), '"\'');
                break;

            case 'parameters':
                $key   = 'parameters';
                $value = [];
                self::generateSchemaBody($node->schemaBody, $value);
                break;

            case 'request':
                $key   = 'request';
                $value = [];
                self::generateSchemaBody($node->schemaBody, $value);
                break;

            case 'response':
                $key   = 'response';
                $value = [];
                self::generateSchemaBody($node->schemaBody, $value);
                break;

            case 'callback':
                $key   = 'callback';
                $value = $node->callable->value();
                break;

            case 'empty':
            default:
                return;
        }

        // A field is defined twice
        if (array_key_exists($key, $result)) {
            $msg = "Property '$key' can only be defined once.";
            throw new CompilationErrorException($msg);
        }

        $result[$key] = $value;
        self::generateRouteBody($node->routeBody, $result);
    }

    /**
     * Fills an array with the informations contained in a schema's body.
     *
     * @param SyntaxNode $node The syntax node corresponding to the schema's
     * body.
     * @param array $result The array to fill.
     * @return void
     */
    private static function generateSchemaBody(
        SyntaxNode $node,
        array &$result
    ): void {
        switch ($node->pattern()) {
            case 'field':
                self::generateSchemaField($node->schemaField, $result);
                break;
            case 'empty':
            default:
                return;
        }

        self::generateSchemaBody($node->schemaBody, $result);
    }

    /**
     * Fills an array with the informations of a schema field.
     *
     * @param SyntaxNode $node The syntax node corresponding to the schema
     * field.
     * @param array $result The array to fill.
     * @return void
     */
    private static function generateSchemaField(
        SyntaxNode $node,
        array &$result
    ): void {
        $name   = '';
        $field  = [];
        $isList = $node->type->pattern() == 'list';

        switch ($node->pattern()) {
            case 'optional':
                $default = $node->value->value();
                $field['default'] = trim($default, '"\'');

                if ($default === '[]') {
                    $field['default'] = [];
                }

                if ($default === 'null') {
                    $field['default'] = null;
                }

            case 'mandatory':
                $name   = $node->identifier->value();
                $type   = $node->type->value();

                $field['isList'] = $isList;
                $field['type']   = $isList ? substr($type, 0, -2) : $type;

                self::generateSchemaFieldDoc($node->schemaFieldDoc, $field);
                break;
            default:
                return;
        }

        if (array_key_exists($name, $result)) {
            $msg = "Field '$name' is defined more than once.";
            throw new CompilationErrorException($msg);
        }

        if (!empty($name)) {
            $result[$name] = $field;
        }
    }

    /**
     * Fills an array with the informations of a schema field's documentation.
     *
     * @param SyntaxNode $node The syntax node corresponding to the schema
     * field's documentation.
     * @param array $result The array to fill.
     * @return void
     */
    private static function generateSchemaFieldDoc(
        SyntaxNode $node,
        array &$result
    ): void {
        switch ($node->pattern()) {
            case 'doc':
                $documentation = trim($node->string->value(), '"\'');
                $result['documentation'] = $documentation;
                break;
            case 'empty':
            default:
                return;
        }
    }
}
