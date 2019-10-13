<?php

declare(strict_types=1);

namespace Nucleus\Filter\Exceptions;

class MissingTypeException extends \Exception
{
    private $property;

    public function __construct(string $property)
    {
        parent::__construct("Missing type for property $property.");
        $this->property = $property;
    }

    public function property(): string
    {
        return $this->property;
    }
}
