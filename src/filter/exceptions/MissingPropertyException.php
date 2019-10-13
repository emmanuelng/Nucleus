<?php

declare(strict_types=1);

namespace Nucleus\Filter\Exceptions;

class MissingPropertyException extends \Exception
{
    private $property;

    public function __construct(string $property)
    {
        parent::__construct("Missing property $property.");
        $this->property = $property;
    }

    public function property(): string
    {
        return $this->property;
    }
}