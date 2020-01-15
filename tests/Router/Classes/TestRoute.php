<?php

declare(strict_types=1);

namespace Tests\Router\Classes;

use Nucleus\Router\Request;
use Nucleus\Router\Response;
use Nucleus\Router\Route;
use Nucleus\Types\Field;
use Nucleus\Types\Schema;
use Nucleus\Types\Type;
use Nucleus\Types\Types\BooleanType;
use Nucleus\Types\Types\FloatType;
use Nucleus\Types\Types\IntegerType;
use Nucleus\Types\Types\StringType;

/**
 * This class is used to test the route class.
 */
class TestRoute implements Route
{
    /**
     * The request method.
     *
     * @var string
     */
    private $method;

    /**
     * The URL.
     *
     * @var string
     */
    private $url;

    /**
     * The callback to execute when the route is executed.
     *
     * @var callable
     */
    private $onExecute;

    /**
     * The parameter schema.
     *
     * @var Schema
     */
    private $parameters;

    /**
     * The request schema.
     *
     * @var Schema
     */
    private $request;

    /**
     * The response schema.
     *
     * @var Schema
     */
    private $response;

    /**
     * Indicates whether the route was executed.
     *
     * @var bool
     */
    private $wasExecuted;

    /**
     * The request object received in the `execute()` method.
     *
     * @var Request
     */
    private $receivedRequest;

    /**
     * Initializes the test route.
     *
     * @param string $method The request method.
     * @param string $url The URL.
     * @param callable $onExecute The callback to execute when the route
     * is executed.
     */
    public function __construct(
        string $method,
        string $url,
        callable $onExecute = null
    ) {
        $this->method           = $method;
        $this->url              = $url;
        $this->onExecute        = $onExecute;
        $this->parameters       = new Schema();
        $this->request          = new Schema();
        $this->response         = new Schema();
        $this->wasExecuted      = false;
        $this->receivedRequest  = null;
    }

    /**
     * Sets the parameter schema of the route.
     *
     * @param array $schema The schema.
     * @return void
     */
    public function setParameterSchema(array $schema): void
    {
        $this->parameters = self::buildSchema($schema);
    }

    /**
     * Sets the request schema.
     *
     * @param array $schema The schema.
     * @return void
     */
    public function setRequestSchema(array $schema): void
    {
        $this->request = self::buildSchema($schema);
    }

    /**
     * Sets the response schema.
     *
     * @param array $schema The schema
     * @return void
     */
    public function setResponseSchema(array $schema): void
    {
        $this->response = self::buildSchema($schema);
    }

    /**
     * {@inheritDoc}
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * {@inheritDoc}
     */
    public function url(): string
    {
        return $this->url;
    }

    /**
     * {@inheritDoc}
     */
    public function parameters(): Schema
    {
        return $this->parameters;
    }

    /**
     * {@inheritDoc}
     */
    public function requestBody(): Schema
    {
        return $this->request;
    }

    /**
     * {@inheritDoc}
     */
    public function responseBody(): Schema
    {
        return $this->response;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(Request $req, Response $res): void
    {
        $this->wasExecuted     = true;
        $this->receivedRequest = $req;

        // Execute the callback
        if ($this->onExecute !== null) {
            call_user_func($this->onExecute, $req, $res);
        }
    }

    /**
     * Returns whether the route was executed or not.
     *
     * @return boolean True if the route was executed, false otherwise.
     */
    public function wasExecuted(): bool
    {
        return $this->wasExecuted;
    }

    /**
     * Returns the request object received in the `execute()` method. Returns
     * null if the route was not executed.
     *
     * @return Request|null
     */
    public function receivedRequest(): ?Request
    {
        return $this->receivedRequest;
    }

    /**
     * Builds a schema object from an array.
     *
     * @param array $schemaArr The array.
     * @return Schema The schema object.
     */
    private static function buildSchema(array $schemaArr): Schema
    {
        $schema = new Schema();

        foreach ($schemaArr as $name => $fieldArr) {
            $type   = self::buildType($fieldArr['type']);
            $isList = $fieldArr['isList'] ?? false;
            $field  = new Field($name, $type, $isList);

            if (isset($fieldArr['default'])) {
                $default = $fieldArr['default'];
                $field->setDefaultValue($default);
            }

            $schema->addField($field);
        }

        return $schema;
    }

    /**
     * Builds a type object based on an input. If the input is an array,
     * returns a schema, else returns a base type, if it exists.
     *
     * @param string|array $type The input.
     * @return Type|null The type object or nullif it is impossible to build
     * it.
     */
    private static function buildType($type): ?Type
    {
        // Array. Return a schema.
        if (is_array($type)) {
            return self::buildSchema($type);
        }

        // Other. Try to return a built-in type.
        switch ($type) {
            case 'int':
                return IntegerType::get();
            case 'bool':
                return BooleanType::get();
            case 'float':
                return FloatType::get();
            case 'string':
                return StringType::get();
            default:
                break;
        }

        // Invalid type.
        return null;
    }
}
